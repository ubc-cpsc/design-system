
(function ($, Drupal) {

  Drupal.behaviors.BackToTop = {
    attach: function () {
      var $window = $(window);
      var $backToTop = $('#back-to-top');
      $backToTop.hide();

      $window.scroll(function () {
        $window.scrollTop() > 150 ? $backToTop.fadeIn(300) : $backToTop.fadeOut(300);
      });
      $backToTop.find('a.ease').on('click', function (e) {
        e.preventDefault();
        $('html, body').animate({scrollTop: 0}, 800);
        return false;
      });
    }
  };

  Drupal.behaviors.SearchFocus = {
    attach: function (context) {
      var checkIsVisible;
      $('#ubc7-global-utility', context).on('click', 'button', function () {
        clearInterval(checkIsVisible);
      });
      $('#ubc7-global-utility', context).on('click', 'button[data-toggle="collapse"]', function () {
        var target = $(this).data('target');
        var $searchInput = $(target).find('input[type="text"]');
        if ($searchInput.length) {
          checkIsVisible = setInterval(function () {
            if ($searchInput.filter(':visible').length) {
              clearInterval(checkIsVisible);
              $searchInput.focus();
              console.log($searchInput);
            }
          }, 100);
        }
      });
    }
  };

  Drupal.behaviors.SubMenu = {
    attach: function (context) {
      var $subMenu = $('.sub-menu', context);
      var $toggles = $subMenu.find('.menu-item__toggle');
      $toggles.on('click', function () {
        $(this).closest('li').toggleClass('menu-item--expand');
      });
    }
  };

  Drupal.behaviors.CourseInfoToggle = {
    attach: function (context, settings) {
      $('.course-section-table-body > tr:not(.child)', context).click(function (e) {
        // Exclude links.
        if (e.target.tagName === "A") {
          return;
        }
        e.preventDefault();
        $(this).next('tr').toggle();
        $(this).toggleClass('open');
        $(this).children("td:last").children('.chevron').toggleClass('bottom');
      });
    }
  };

} (jQuery, Drupal));
