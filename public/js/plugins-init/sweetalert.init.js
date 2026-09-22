(function($) {
    "use strict";
    var bindClick = function(selector, fn) {
        var el = document.querySelector(selector);
        if (el) { el.onclick = fn; }
    };

    bindClick(".sweet-wrong", function () { Swal.fire("Oops...", "Something went wrong !!", "error"); });
    bindClick(".sweet-message", function () { Swal.fire("Hey, Here's a message !!"); });
    bindClick(".sweet-text", function () { Swal.fire("Hey, Here's a message !!", "It's pretty, isn't it?"); });
    bindClick(".sweet-success", function () { Swal.fire("Hey, Good job !!", "You clicked the button !!", "success"); });
    bindClick(".sweet-confirm", function () { Swal.fire({ title: "Are you sure to delete ?", text: "You will not be able to recover this imaginary file !!", type: "warning", showCancelButton: !0, confirmButtonColor: "#DD6B55", confirmButtonText: "Yes, delete it !!", closeOnConfirm: !1 }, function () { Swal.fire("Deleted !!", "Hey, your imaginary file has been deleted !!", "success"); }); });
    bindClick(".sweet-success-cancel", function () { Swal.fire({ title: "Are you sure to delete ?", text: "You will not be able to recover this imaginary file !!", type: "warning", showCancelButton: !0, confirmButtonColor: "#DD6B55", confirmButtonText: "Yes, delete it !!", cancelButtonText: "No, cancel it !!", closeOnConfirm: !1, closeOnCancel: !1 }, function (e) { if (e) { Swal.fire("Deleted !!", "Hey, your imaginary file has been deleted !!", "success"); } else { Swal.fire("Cancelled !!", "Hey, your imaginary file is safe !!", "error"); } }); });
    bindClick(".sweet-image-message", function () { Swal.fire({ title: "Sweet !!", text: "Hey, Here's a custom image !!", imageUrl: "images/hand.png", imageWidth : "20%" }); });
    bindClick(".sweet-html", function () { Swal.fire({ title: "Sweet !!", text: "<span style='color:#ff0000'>Hey, you are using HTML !!<span>", html: !0 }); });
    bindClick(".sweet-auto", function () { Swal.fire({ title: "Sweet auto close alert !!", text: "Hey, i will close in 2 seconds !!", timer: 2e3, showConfirmButton: !1 }); });
    bindClick(".sweet-prompt", function () { Swal.fire({ title: "Enter an input !!", text: "Write something interesting !!", type: "input", showCancelButton: !0, closeOnConfirm: !1, animation: "slide-from-top", inputPlaceholder: "Write something" }, function (e) { return !1 !== e && ("" === e ? (swal.showInputError("You need to write something!"), !1) : void Swal.fire("Hey !!", "You wrote: " + e, "success")); }); });
    bindClick(".sweet-ajax", function () { Swal.fire({ title: "Sweet ajax request !!", text: "Submit to run ajax request !!", type: "info", showCancelButton: !0, closeOnConfirm: !1, showLoaderOnConfirm: !0 }, function () { setTimeout(function () { Swal.fire("Hey, your ajax request finished !!"); }, 2e3); }); });
})(jQuery);