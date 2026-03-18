$(document).ready(function() {
    $('.navbar').on('scroll', function() {
        if ($(this).scrollTop() > 50) {
            $(this).addClass('scrolled');
        } else {
            $(this).removeClass('scrolled');
        }
    });
    
    $(window).on('scroll', function() {
        if ($(this).scrollTop() > 300) {
            $('#scrollTop').addClass('visible');
        } else {
            $('#scrollTop').removeClass('visible');
        }
    });
    
    $('#scrollTop').on('click', function(e) {
        e.preventDefault();
        $('html, body').animate({ scrollTop: 0 }, 500);
    });
    
    $('.search-input').on('keyup', function(e) {
        const query = $(this).val().trim();
        if (query.length >= 2) {
            clearTimeout($.data(this, 'searchTimer'));
            $(this).data('searchTimer', setTimeout(function() {
                performSearch(query);
            }, 300));
        }
    });
    
    function performSearch(query) {
        $.ajax({
            url: 'search.php',
            method: 'GET',
            data: { q: query, ajax: 1 },
            success: function(response) {
                $('#searchResults').html(response).show();
            }
        });
    }
    
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-input, #searchResults').length) {
            $('#searchResults').hide();
        }
    });
    
    if ($('.article-body').length) {
        const headings = $('.article-body h2, .article-body h3');
        
        if (headings.length > 0) {
            let tocHtml = '<div class="toc-container"><h4><i class="fas fa-list-ul me-2"></i>Daftar Isi</h4><ul class="toc-list">';
            
            headings.each(function() {
                const text = $(this).text();
                const id = text.toLowerCase().replace(/[^a-z0-9]+/g, '-');
                $(this).attr('id', id);
                
                const level = $(this)[0].tagName.toLowerCase();
                const indent = level === 'h3' ? ' style="margin-left: 20px;"' : '';
                
                tocHtml += `<li${indent}><a href="#${id}" class="toc-link">${text}</a></li>`;
            });
            
            tocHtml += '</ul></div>';
            $('.article-body').prepend(tocHtml);
        }
    }
    
    $('.toc-link').on('click', function(e) {
        e.preventDefault();
        const target = $(this).attr('href');
        const offset = 80;
        
        $('html, body').animate({
            scrollTop: $(target).offset().top - offset
        }, 500);
    });
    
    const readingProgress = $('.reading-progress');
    if (readingProgress.length) {
        $(window).on('scroll', function() {
            const scrollTop = $(this).scrollTop();
            const docHeight = $(document).height();
            const winHeight = $(this).height();
            const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
            
            readingProgress.css('width', scrollPercent + '%');
        });
    }
    
    $('.share-btn').on('click', function(e) {
        e.preventDefault();
        
        const platform = $(this).data('platform');
        const url = encodeURIComponent(window.location.href);
        const title = encodeURIComponent(document.title);
        
        let shareUrl;
        
        switch (platform) {
            case 'facebook':
                shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
                break;
            case 'twitter':
                shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
                break;
            case 'linkedin':
                shareUrl = `https://www.linkedin.com/shareArticle?url=${url}&title=${title}`;
                break;
            case 'whatsapp':
                shareUrl = `https://wa.me/?text=${title} ${url}`;
                break;
        }
        
        window.open(shareUrl, '_blank', 'width=600,height=400');
    });
    
    $('.article-card, .category-card').on('mouseenter', function() {
        $(this).addClass('hovered');
    }).on('mouseleave', function() {
        $(this).removeClass('hovered');
    });
    
    if ($('.lazy-load').length) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    const img = $(entry.target);
                    const src = img.data('src');
                    
                    if (src) {
                        img.attr('src', src);
                        img.removeClass('lazy-load');
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, observerOptions);
        
        $('.lazy-load').each(function() {
            observer.observe(this);
        });
    }
    
    const copyLinkBtn = $('.copy-link-btn');
    if (copyLinkBtn.length) {
        copyLinkBtn.on('click', function(e) {
            e.preventDefault();
            
            const url = window.location.href;
            
            navigator.clipboard.writeText(url).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Link artikel berhasil disalin!',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(function() {
                const input = $('<input>');
                $('body').append(input);
                input.val(url).select();
                document.execCommand('copy');
                input.remove();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Link artikel berhasil disalin!',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        });
    }
    
    if ($('#newsletterForm').length) {
        $('#newsletterForm').on('submit', function(e) {
            e.preventDefault();
            
            const email = $(this).find('input[type="email"]').val();
            
            if (!email) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Mohon masukkan email Anda',
                    confirmButtonColor: '#8B5CF6'
                });
                return;
            }
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: { email: email },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Terima Kasih!',
                        text: 'Anda telah berlangganan newsletter kami',
                        timer: 3000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                    
                    $('#newsletterForm')[0].reset();
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonColor: '#8B5CF6'
                    });
                }
            });
        });
    }
});

function formatNumber(num) {
    if (num >= 1000000) {
        return (num / 1000000).toFixed(1) + 'M';
    } else if (num >= 1000) {
        return (num / 1000).toFixed(1) + 'K';
    }
    return num.toString();
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

const searchDebounce = debounce(performSearch, 300);

function setCookie(name, value, days) {
    const expires = new Date();
    expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
}

function getCookie(name) {
    const nameEQ = name + '=';
    const ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function deleteCookie(name) {
    document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}