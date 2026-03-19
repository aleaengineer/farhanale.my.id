$(document).ready(function() {
    $('#toggleSidebar').on('click', function() {
        $('.admin-sidebar').toggleClass('show');

        if ($(window).width() < 992) {
            if ($('.admin-sidebar').hasClass('show')) {
                $('body').append('<div class="sidebar-overlay"></div>');
                $('.sidebar-overlay').on('click', function() {
                    $('.admin-sidebar').removeClass('show');
                    $(this).remove();
                });
            } else {
                $('.sidebar-overlay').remove();
            }
        }
    });

    $(document).on('click', function(e) {
        if ($(window).width() < 992) {
            if (!$(e.target).closest('.admin-sidebar').length && !$(e.target).closest('#toggleSidebar').length) {
                $('.admin-sidebar').removeClass('show');
                $('.sidebar-overlay').remove();
            }
        }
    });
    
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: 'Data ini akan dihapus secara permanen!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = href;
            }
        });
    });
    
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: '#contentEditor',
            height: 400,
            plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen',
            toolbar: 'undo redo | blocks bold italic forecolor | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat',
            menubar: false,
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }'
        });
    }
    
    $('.form-control, .form-select').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });
    
    $('input[type="file"]').on('change', function() {
        const file = this.files[0];
        if (file) {
            const fileSize = file.size / 1024 / 1024;
            if (fileSize > 2) {
                Swal.fire({
                    icon: 'error',
                    title: 'File Terlalu Besar',
                    text: 'Maksimal ukuran file adalah 2MB',
                    confirmButtonColor: '#8B5CF6'
                });
                $(this).val('');
            }
        }
    });
    
    $('.image-upload').on('change', function() {
        const input = this;
        const preview = $(input).siblings('.preview-image');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.attr('src', e.target.result);
                preview.show();
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    });
    
    $(document).ajaxStart(function() {
        $('body').append('<div class="ajax-loader"><div class="spinner"></div></div>');
    });
    
    $(document).ajaxStop(function() {
        $('.ajax-loader').remove();
    });
});

$(window).on('scroll', function() {
    const scrollTop = $(this).scrollTop();
    
    if (scrollTop > 100) {
        $('.scroll-top').addClass('visible');
    } else {
        $('.scroll-top').removeClass('visible');
    }
});

$('.scroll-top').on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({ scrollTop: 0 }, 500);
});