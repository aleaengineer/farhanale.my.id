$(document).ready(function() {
    AOS.init({
        duration: 800,
        once: true
    });

    var currentPage = window.location.pathname.split('/').pop();
    var isIndexPage = currentPage === '' || currentPage === 'index.php';
    
    if (!isIndexPage) {
        $('#mainNav').addClass('scrolled');
    }

    $(window).scroll(function() {
        if ($(this).scrollTop() > 50) {
            $('#mainNav').addClass('scrolled');
        } else {
            if (isIndexPage) {
                $('#mainNav').removeClass('scrolled');
            }
        }
    });

    $('a[href^="#"]').on('click', function(e) {
        e.preventDefault();
        var target = $(this.getAttribute('href'));
        if (target.length) {
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 80
            }, 800);
        }
    });

    $('.stat-number').each(function() {
        var $this = $(this);
        var countTo = $this.attr('data-count');
        
        $({ countNum: $this.text() }).animate({
            countNum: countTo
        }, {
            duration: 2000,
            easing: 'linear',
            step: function() {
                $this.text(Math.floor(this.countNum) + '+');
            },
            complete: function() {
                $this.text(this.countNum + '+');
            }
        });
    });

    $('.filter-btn').click(function() {
        var filterValue = $(this).attr('data-filter');
        
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        if (filterValue === 'all') {
            $('.portfolio-card').fadeIn(300);
        } else {
            $('.portfolio-card').hide();
            $('.portfolio-card[data-category="' + filterValue + '"]').fadeIn(300);
        }
    });

    $('#contactForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        var submitBtn = $(this).find('button[type="submit"]');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
        
        $.ajax({
            url: 'contact.php',
            type: 'POST',
            data: formData + '&action=send',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#contactForm')[0].reset();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
                submitBtn.prop('disabled', false).html('Send Message');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.'
                });
                submitBtn.prop('disabled', false).html('Send Message');
            }
        });
    });

    if ($('#blogSearch').length) {
        $('#blogSearch').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            
            $('.blog-card').filter(function() {
                var title = $(this).find('.blog-title').text().toLowerCase();
                var excerpt = $(this).find('.blog-excerpt').text().toLowerCase();
                $(this).toggle(title.indexOf(value) > -1 || excerpt.indexOf(value) > -1);
            });
        });
    }

    if ($('.portfolio-image').length) {
        $('.portfolio-image').click(function() {
            var imgSrc = $(this).find('img').attr('src');
            var title = $(this).siblings('.portfolio-content').find('.portfolio-title').text();
            var description = $(this).siblings('.portfolio-content').find('.portfolio-description').text();
            
            Swal.fire({
                imageUrl: imgSrc,
                imageHeight: 300,
                title: title,
                text: description,
                width: '600px',
                padding: '20px',
                showConfirmButton: false,
                showCloseButton: true
            });
        });
    }

    var navbarCollapse = $('.navbar-collapse');
    
    $('.navbar-toggler').on('click', function() {
        setTimeout(function() {
            if (navbarCollapse.hasClass('show')) {
                if (!$('.navbar').hasClass('scrolled')) {
                    $('.navbar').addClass('scrolled');
                }
            } else {
                var currentPage = window.location.pathname.split('/').pop();
                var isIndexPage = currentPage === '' || currentPage === 'index.php';
                
                if (isIndexPage && $(window).scrollTop() <= 50) {
                    $('.navbar').removeClass('scrolled');
                }
            }
        }, 100);
    });
    
    $(document).on('click', function(e) {
        if ($(window).width() < 992) {
            if (!$(e.target).closest('.navbar').length && navbarCollapse.hasClass('show')) {
                var bsCollapse = new bootstrap.Collapse(navbarCollapse[0], {
                    toggle: false
                });
                bsCollapse.hide();
                
                var currentPage = window.location.pathname.split('/').pop();
                var isIndexPage = currentPage === '' || currentPage === 'index.php';
                
                if (isIndexPage && $(window).scrollTop() <= 50) {
                    $('.navbar').removeClass('scrolled');
                }
            }
        }
    });

    $('.nav-link').on('click', function() {
        if ($(window).width() < 992) {
            var bsCollapse = new bootstrap.Collapse(navbarCollapse[0], {
                toggle: false
            });
            bsCollapse.hide();
            
            var currentPage = window.location.pathname.split('/').pop();
            var isIndexPage = currentPage === '' || currentPage === 'index.php';
            
            if (isIndexPage && $(window).scrollTop() <= 50) {
                $('.navbar').removeClass('scrolled');
            }
        }
    });

    if ($('.skill-card').length) {
        var animated = false;
        
        $(window).scroll(function() {
            var scrollTop = $(window).scrollTop();
            var elementOffset = $('.skill-card').first().offset().top;
            var distance = (elementOffset - scrollTop);
            
            if (distance < $(window).height() && !animated) {
                animated = true;
                animateSkills();
            }
        });
    }
    
    function animateSkills() {
        $('.progress-bar').each(function() {
            var width = $(this).attr('aria-valuenow');
            $(this).css('width', width + '%');
        });
    }
    
    if ($(window).scrollTop() > 0) {
        $('#mainNav').addClass('scrolled');
    }
});
