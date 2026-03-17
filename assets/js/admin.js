$(document).ready(function() {
    if ($('#sidebarToggle').length) {
        $('#sidebarToggle').click(function(e) {
            e.stopPropagation();
            $('.admin-sidebar').toggleClass('show');
            toggleOverlay();
        });
    }
    
    function toggleOverlay() {
        if ($('.admin-sidebar').hasClass('show')) {
            $('body').append('<div class="sidebar-overlay active"></div>');
            $('.sidebar-overlay').click(function() {
                $('.admin-sidebar').removeClass('show');
                $(this).remove();
            });
        } else {
            $('.sidebar-overlay').remove();
        }
    }

    $('.btn-delete').click(function() {
        var id = $(this).data('id');
        var type = $(this).data('type');
        var title = $(this).data('title');
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this! Delete ' + title + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#8B5CF6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '',
                    type: 'POST',
                    data: {
                        action: 'delete',
                        id: id,
                        type: type
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.'
                        });
                    }
                });
            }
        });
    });

    $('.image-upload').change(function() {
        var file = this.files[0];
        
        if (file) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $(this).siblings('.preview-image').attr('src', e.target.result).show();
            }.bind(this);
            
            reader.readAsDataURL(file);
        }
    });

    if ($('.ckeditor').length) {
        ClassicEditor
            .create(document.querySelector('.ckeditor'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo']
            })
            .catch(error => {
                console.error(error);
            });
    }

    $('.btn-save').click(function() {
        var form = $(this).closest('form');
        var formData = new FormData(form[0]);
        var btn = $(this);
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.ajax({
            url: '',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(function() {
                        location.href = '';
                    }, 2000);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                    btn.prop('disabled', false).html('Save');
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.'
                });
                btn.prop('disabled', false).html('Save');
            }
        });
    });

    if ($('#statsRefresh').length) {
        $('#statsRefresh').click(function() {
            var btn = $(this);
            btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
            
            setTimeout(function() {
                location.reload();
            }, 1000);
        });
    }

    $('input[type="file"]').change(function() {
        var file = this.files[0];
        var maxSize = 2 * 1024 * 1024;
        
        if (file && file.size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File too large!',
                text: 'Please upload an image smaller than 2MB.'
            });
            $(this).val('');
        }
    });

    if ($('.admin-menu-item').length) {
        var currentPath = window.location.pathname;
        var filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
        
        $('.admin-menu-item').each(function() {
            var href = $(this).attr('href');
            if (href === filename) {
                $(this).addClass('active');
            }
        });
        
        $('.admin-menu-item').click(function(e) {
            if ($(window).width() < 992) {
                $('.admin-sidebar').removeClass('show');
                $('.sidebar-overlay').remove();
            }
        });
    }
});
