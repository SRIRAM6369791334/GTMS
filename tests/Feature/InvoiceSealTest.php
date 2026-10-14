<?php

namespace Tests\Feature;

use App\Http\Controllers\Accounts\InvoiceSealController;
use App\Models\Customer;
use App\Models\District;
use App\Models\Mineral;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class InvoiceSealTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;
    protected Customer $customer;
    protected District $district;
    protected Mineral $mineral;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'gtms_data',
        ]);

        $this->user = User::whereHas('roles', fn($q) => $q->where('name', 'Admin'))->first()
            ?: User::where('role_id', 1)->first()
            ?: User::first();

        if ($this->user && !$this->user->hasRole(['Admin', 'Super Admin'])) {
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
            $this->user->assignRole($adminRole);
        }

        $this->actingAs($this->user);

        // Ensure clean test environment for custom seal uploads
        $dir = public_path('uploads/invoice_seals');
        if (file_exists($dir)) {
            foreach (glob($dir . '/custom_seal.*') as $f) {
                @unlink($f);
            }
        }

        $this->district = District::firstOrCreate(['name' => 'Salem']);
        $this->mineral = Mineral::firstOrCreate(['name' => 'Rough Stone and Gravel']);

        $rnd = rand(10000, 99999);
        $this->customer = Customer::create([
            'customer_name' => 'Test Quarry Proprietor ' . $rnd,
            'company_name'  => 'Test Quarry Minerals Corp ' . $rnd,
            'district_id'   => $this->district->id,
            'mineral_id'    => $this->mineral->id,
            'mobile_num'    => '99887' . rand(10000, 99999),
            'address'       => '123 Mining Road, Dharmapuri',
            'slug'          => 'test-quarry-corp-' . $rnd,
        ]);
    }

    protected function tearDown(): void
    {
        // Cleanup after test
        $dir = public_path('uploads/invoice_seals');
        if (file_exists($dir)) {
            foreach (glob($dir . '/custom_seal.*') as $f) {
                @unlink($f);
            }
        }

        parent::tearDown();
    }

    /**
     * Test 1: Invoices render clean empty white space when no custom seal image is uploaded.
     */
    public function test_invoices_render_empty_white_space_by_default_when_no_seal_uploaded(): void
    {
        $this->assertNull(InvoiceSealController::getCustomSealUrl());

        // Check Customer Tracking Tax Invoice
        $resTax = $this->get(route('customer-tracking.tax-invoice', $this->customer->slug));
        $resTax->assertStatus(200);
        $resTax->assertSee('seal-empty-space');
        $resTax->assertSee('Seal: Blank (Manual)');
        $resTax->assertDontSee('images/invoices/gtms_seal.png');

        // Check Customer Tracking Proforma Invoice
        $resPi = $this->get(route('customer-tracking.proforma-invoice', $this->customer->slug));
        $resPi->assertStatus(200);
        $resPi->assertSee('seal-empty-space');
        $resPi->assertSee('Seal: Blank (Manual)');
        $resPi->assertDontSee('images/invoices/gtms_seal.png');
    }

    /**
     * Test 2: User can manually upload a custom seal image via AJAX endpoint.
     */
    public function test_user_can_upload_custom_seal_and_signature_image(): void
    {
        $file = UploadedFile::fake()->image('my_round_seal.png', 400, 200);

        $response = $this->postJson(route('accounts.invoices.seal.upload'), [
            'seal_image' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $url = InvoiceSealController::getCustomSealUrl();
        $this->assertNotNull($url);
        $this->assertStringContainsString('custom_seal', $url);

        // Check status endpoint
        $statusRes = $this->getJson(route('accounts.invoices.seal.status'));
        $statusRes->assertStatus(200);
        $statusRes->assertJson([
            'has_seal' => true,
        ]);
    }

    /**
     * Test 3: Uploaded seal displays on Tax Invoice and Proforma Invoice once uploaded.
     */
    public function test_uploaded_seal_is_injected_into_invoices(): void
    {
        $file = UploadedFile::fake()->image('director_seal.png', 300, 150);
        $this->postJson(route('accounts.invoices.seal.upload'), [
            'seal_image' => $file,
        ]);

        $sealUrl = InvoiceSealController::getCustomSealUrl();
        $this->assertNotNull($sealUrl);

        // Tax Invoice should now have seal image url
        $resTax = $this->get(route('customer-tracking.tax-invoice', $this->customer->slug));
        $resTax->assertStatus(200);
        $resTax->assertSee('custom_seal');

        // Proforma Invoice should also have seal image url
        $resPi = $this->get(route('customer-tracking.proforma-invoice', $this->customer->slug));
        $resPi->assertStatus(200);
        $resPi->assertSee('custom_seal');
    }

    /**
     * Test 4: User can remove custom seal image, reverting invoices back to empty white space.
     */
    public function test_user_can_remove_seal_image_to_revert_to_blank_white_space(): void
    {
        // First upload
        $file = UploadedFile::fake()->image('temp_seal.png', 300, 150);
        $this->postJson(route('accounts.invoices.seal.upload'), [
            'seal_image' => $file,
        ]);
        $this->assertNotNull(InvoiceSealController::getCustomSealUrl());

        // Now remove
        $removeRes = $this->postJson(route('accounts.invoices.seal.remove'));
        $removeRes->assertStatus(200);
        $removeRes->assertJson([
            'success' => true,
        ]);

        // Should now be null
        $this->assertNull(InvoiceSealController::getCustomSealUrl());

        // Invoices should now show empty white space again
        $resTax = $this->get(route('customer-tracking.tax-invoice', $this->customer->slug));
        $resTax->assertStatus(200);
        $resTax->assertSee('seal-empty-space');
    }

    /**
     * Test 5: Rejection on invalid non-image file uploads.
     */
    public function test_upload_validates_and_rejects_non_image_files(): void
    {
        $badFile = UploadedFile::fake()->create('malicious.exe', 100, 'application/octet-stream');

        $response = $this->postJson(route('accounts.invoices.seal.upload'), [
            'seal_image' => $badFile,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['seal_image']);
    }
}
