<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification;
use Carbon\Carbon;

class Notification extends DatabaseNotification
{
    protected $table = 'notifications';

    /**
     * Get notification title safely from data payload
     */
    public function getTitleAttribute(): string
    {
        return $this->data['title'] ?? 'System Notification';
    }

    /**
     * Get notification message / summary from data payload
     */
    public function getMessageAttribute(): string
    {
        return $this->data['message'] ?? ($this->data['description'] ?? '');
    }

    /**
     * Get operational category: lease, mining, survey, drone, environment, accounts, system
     */
    public function getCategoryAttribute(): string
    {
        return strtolower($this->data['category'] ?? ($this->data['type'] ?? 'system'));
    }

    /**
     * Get destination action URL
     */
    public function getActionUrlAttribute(): ?string
    {
        return $this->data['action_url'] ?? ($this->data['url'] ?? null);
    }

    /**
     * Get category-appropriate Bootstrap icon
     */
    public function getIconAttribute(): string
    {
        if (!empty($this->data['icon'])) {
            return $this->data['icon'];
        }

        return match ($this->category) {
            'lease' => 'bi-file-earmark-text',
            'mining' => 'bi-geo-alt',
            'survey', 'dgps' => 'bi-check-circle',
            'drone' => 'bi-send',
            'environment', 'ec' => 'bi-leaf',
            'accounts', 'finance' => 'bi-credit-card-2-front',
            default => 'bi-bell',
        };
    }

    /**
     * Get category styling palette (background, text, border)
     */
    public function getStyleConfigAttribute(): array
    {
        return match ($this->category) {
            'lease' => [
                'bg' => '#eff6ff',
                'text' => '#1d4ed8',
                'border' => '#bfdbfe',
                'label' => 'Lease Application',
                'chip_class' => 'bg-primary-subtle text-primary border-primary-subtle',
            ],
            'mining' => [
                'bg' => '#fffbeb',
                'text' => '#b45309',
                'border' => '#fde68a',
                'label' => 'Mining Plan',
                'chip_class' => 'bg-warning-subtle text-warning border-warning-subtle',
            ],
            'survey', 'dgps' => [
                'bg' => '#ecfdf5',
                'text' => '#047857',
                'border' => '#a7f3d0',
                'label' => 'DGPS Survey',
                'chip_class' => 'bg-success-subtle text-success border-success-subtle',
            ],
            'drone' => [
                'bg' => '#faf5ff',
                'text' => '#7c3aed',
                'border' => '#e9d5ff',
                'label' => 'Drone Demarcation',
                'chip_class' => 'bg-purple-subtle text-purple border-purple-subtle',
            ],
            'environment', 'ec' => [
                'bg' => '#f0fdf4',
                'text' => '#15803d',
                'border' => '#bbf7d0',
                'label' => 'Environment Clearance',
                'chip_class' => 'bg-success-subtle text-success border-success-subtle',
            ],
            'accounts', 'finance' => [
                'bg' => '#eef2ff',
                'text' => '#4338ca',
                'border' => '#c7d2fe',
                'label' => 'Accounts & Billing',
                'chip_class' => 'bg-indigo-subtle text-indigo border-indigo-subtle',
            ],
            default => [
                'bg' => '#f8fafc',
                'text' => '#334155',
                'border' => '#cbd5e1',
                'label' => 'System Alert',
                'chip_class' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
            ],
        };
    }

    /**
     * Check if notification is unread
     */
    public function getIsReadAttribute(): bool
    {
        return !is_null($this->read_at);
    }

    /**
     * Human-readable time
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Just now';
    }

    /**
     * Scope for category filtering
     */
    public function scopeCategory($query, string $category)
    {
        if ($category === 'all' || empty($category)) {
            return $query;
        }

        return $query->where(function ($q) use ($category) {
            $q->where('data->category', $category)
              ->orWhere('data->type', $category);
        });
    }

    /**
     * Scope for unread
     */
    public function scopeUnreadOnly($query)
    {
        return $query->whereNull('read_at');
    }

    /**
     * Scope for read
     */
    public function scopeReadOnly($query)
    {
        return $query->whereNotNull('read_at');
    }
}
