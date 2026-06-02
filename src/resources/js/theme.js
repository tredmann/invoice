//NAVIGATION
$(document).ready(function () {
    //RESPONSIVE NAV BAR
    $('.navbar-burger').click(function () {
        $('.navbar-burger').toggleClass('is-active');
        $('.navbar-menu').toggleClass('is-active');
    });

    //NAV PROFILE MENU
    $('#profile_menu_trigger').click(function () {
        const profileMenu = $('#profile_menu');
        profileMenu.toggleClass('is-block');
        profileMenu.toggleClass('is-hidden');
    });

    //CONTEXT MENUS
    $('.context-menu-trigger').click(function () {
        const chosenContextMenu = $(this).siblings('.context-menu');
        if (chosenContextMenu.hasClass('context-menu-active')) {
            chosenContextMenu.removeClass('context-menu-active');
            chosenContextMenu.addClass('context-menu-hidden');
        } else {
            const otherContextMenu = $('.context-menu');
            otherContextMenu.removeClass('context-menu-active');
            otherContextMenu.addClass('context-menu-hidden');
            chosenContextMenu.removeClass('context-menu-hidden');
            chosenContextMenu.addClass('context-menu-active');
        }
    });

    //CLICKABLE ROWS
    $('tbody.is-clickable tr').click(function () {
        window.location.href = $(this).find('input[name=show]')[0].value;
    });

    document.getElementById('sidebar-toggle').addEventListener('click', () => {
        document.getElementById('sidebar').classList.toggle('sidebar--collapsed');
        document.getElementById('sidebar-content').classList.toggle('menu--collapsed');
    });
});

$(window).click(function (event) {
    if (!event.target.matches('.context-menu-active') && !event.target.matches('.context-menu-trigger')) {
        const contextMenu = $('.context-menu-active');
        if (contextMenu.hasClass('context-menu-active')) {
            contextMenu.removeClass('context-menu-active');
            contextMenu.addClass('context-menu-hidden');
        }
    }

    if (
        !event.target.contains(document.getElementById('profile_menu_inital')) &&
        !event.target.matches('.profile_section')
    ) {
        const profileMenu = $('#profile_menu');
        if (profileMenu.hasClass('is-block')) {
            profileMenu.toggleClass('is-block');
            profileMenu.toggleClass('is-hidden');
        }
    }
});
