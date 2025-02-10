$(function() {
    let $wrapper = $('.main-wrapper');
    let $pageWrapper = $('.page-wrapper');
    let $sidebarMenu = $('#sidebar-menu');
    let $mobileBtn = $('#mobile_btn');
    let $sidebarOverlay = $('<div class="sidebar-overlay"></div>');

    // Sidebar
    function initSidebar() {
        $sidebarMenu.on('click', 'a', function(e) {
            let $this = $(this);
            let $parent = $this.parent();

            if ($parent.hasClass('submenu')) {
                e.preventDefault();
            }

            if (!$this.hasClass('subdrop')) {
                $sidebarMenu.find('ul ul').slideUp(350);
                $sidebarMenu.find('a').removeClass('subdrop');
                $this.next('ul').slideDown(350);
                $this.addClass('subdrop');
            } else {
                $this.removeClass('subdrop');
                $this.next('ul').slideUp(350);
            }
        });

        $sidebarMenu.find('ul li.submenu a.active').parents('li:last').children('a:first').addClass('active').trigger('click');
    }

    // Sidebar Initiate
    initSidebar();

    // Mobile menu sidebar overlay
    $('body').append($sidebarOverlay);

    $mobileBtn.on('click', function() {
        $wrapper.toggleClass('slide-nav');
        $sidebarOverlay.toggleClass('opened');
        $('html').toggleClass('menu-opened');
        return false;
    });

    $sidebarOverlay.on('click', function() {
        $wrapper.removeClass('slide-nav');
        $sidebarOverlay.removeClass('opened');
        $('html').removeClass('menu-opened');
    });

    // Set Page Content Height
    function setPageHeight() {
        $pageWrapper.css('min-height', $(window).height());
    }

    setPageHeight();
    $(window).resize(setPageHeight);
});
