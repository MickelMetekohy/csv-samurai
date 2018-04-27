import $ from 'jquery';
import 'bootstrap';
import './../scss/app.scss';

(function closure() {
  let cwPixels = document.body.clientWidth;

  function openNav(navToggle, pageType) {
    if (pageType) {
      $('#site-header').removeClass('bg-transparent');
    } else {
      $('#site-header').addClass('position-absolute');
    }
    $('#site-header').addClass('h-100');
    $('.fa.fa-bars', navToggle)[0].style.display = 'none';
    $('.close-menu', navToggle)[0].style.display = 'inline-block';
    $('span', navToggle).text('Close');
  }

  function closeNav(navToggle, pageType) {
    if (pageType) {
      $('#site-header').addClass('bg-transparent');
    } else {
      $('#site-header').removeClass('position-absolute');
    }
    $('#site-header').removeClass('h-100');
    $('.fa.fa-bars', navToggle)[0].style.display = 'inline-block';
    $('.close-menu', navToggle)[0].style.display = 'none';
    $('span', navToggle).text('');
  }

  function triggerNavClick() {
    if (!$('#site-header .navbar-toggler').hasClass('collapsed')) {
      $('#site-header .navbar-toggler').trigger('click');
    }
  }

  function footerCollapsibleItems() {
    Array.from(document.querySelectorAll('.collapsible-footer-item')).forEach((el) => {
      if (cwPixels >= 768) {
        $(el.dataset.target).collapse('show');
        el.dataset.target = false;
      } else {
        el.dataset.target = `#${el.nextSibling.id}`;
        $(el.dataset.target).collapse('hide');
      }
    });
  }

  // CLICK
  $('#site-header .navbar-toggler').on('click', function (e) {
    e.preventDefault();
    if ($('#site-wrapper').hasClass('home-page')) {
      $(this).hasClass('collapsed') ? openNav(this, true) : closeNav(this, true);
    } else {
      $(this).hasClass('collapsed') ? openNav(this, false) : closeNav(this, false);
    }
  });

  // SCROLL
  $(window).on('scroll', () => {
    if ($(window).scrollTop() >= 3) {
      $('#site-header').addClass('compressed position-fixed');
    } else {
      $('#site-header').removeClass('compressed position-fixed');
    }
  });

  // LOAD
  $(window).on('load', () => {
    setTimeout(() => {
      cwPixels = document.body.clientWidth;
      footerCollapsibleItems();
    }, 100);
  });

  // RESIZE
  $(window).on('resize', () => {
    setTimeout(() => {
      cwPixels = document.body.clientWidth;
      footerCollapsibleItems();
      triggerNavClick();
    }, 100);
  });

}());
