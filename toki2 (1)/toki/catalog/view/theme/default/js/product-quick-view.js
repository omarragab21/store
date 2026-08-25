/*
  Product Quick View
  OpenCart Version: 2.3 - 3.x
  Author: MagDevel (support@magdevel.com)
  Home: https://magdevel.com
*/

$(document).ready(function() {
  if (window.productQuickView) {
    var defaults = {
      text_quick_view: 'Quick View',
      btn_in_cart: 'In Cart',
      text_loading: 'Loading...',
      show_button: 1,
      click_on_image: 0,
      click_on_link: 0,
      replace_button: 0,
    };

    var options = window.productQuickView;

    for (var i in defaults) {
      if (typeof options[i] === 'undefined') {
        options[i] = defaults[i];
      }
    }

    var o = options;

    $('.product-quick-view-data').each(function() {
      var container = $(this).parent().find(".image");
      var productId = $(this).attr("data-pid");

      if (o.show_button) {
        var btnHtml = '<div class="btn-quick-view" onclick="pqvOpenPopup(\'' + productId + '\')">' +
          '<i class="fa fa-eye" aria-hidden="true"></i></div>';

        if (container.length) {
          container.append(btnHtml);

          if (window.productQuickView.text_quick_view && $(window).width() > 991) {
            container.find(".btn-quick-view").tooltip({
              title: window.productQuickView.text_quick_view,
              delay: {
                "show": 100,
                "hide": 0,
              }
            });
          }
        }
      }

      if (o.click_on_image) {
        container.find("a").on("click", function(e) {
          e.preventDefault();
          pqvOpenPopup(productId);
        });
      }

      if (o.click_on_link) {
        $(this).parent().find(".caption a").on("click", function(e) {
          e.preventDefault();
          pqvOpenPopup(productId);
        });
      }
    });
  }
});

function pqvShowLoader() {
  var position = $(window).width() / 2;
  $(".pqv-loader").remove();
  $("body").append('<div class="pqv-loader">' + window.productQuickView.text_loading + '</div>');
  $(".pqv-loader").css("left", position + "px");
}

function pqvHideLoader() {
  $(".pqv-loader").remove();
}

function pqvUpdateMiniCart() {
  $('#cart').load('index.php?route=common/cart/info #cart > *');
}

function pqvChangeImage(imageNumber) {
  var elements = document.getElementsByClassName('pqv-image');
  for (var i = 0; i < elements.length; i++) {
    elements[i].style.display = 'none';
  }
  elements[imageNumber].style.display = 'block';
}

function pqvOpenPopup(product_id) {
  pqvShowLoader();
  $.magnificPopup.open({
    tLoading: false,
    items: {
      src: 'index.php?route=extension/module/product_quick_view/popup&product_id=' + product_id,
      type: 'ajax'
    },
    showCloseBtn: false,
    removalDelay: 300,
    mainClass: 'pqv-mfp-zoom-in',
    callbacks: {
      beforeOpen: function() {
        $('.tooltip').hide();
      },
      ajaxContentAdded: function() {
        pqvHideLoader();
        $('.pqv-zoom').zoom();
        pqvOptionsInit();
        pqvReviewsInit(product_id);
      }
    }
  });
}

function pqvClose() {
  $.magnificPopup.close();
}

function pqvOptionsInit() {
  $('.date').datetimepicker({
    pickTime: false
  });
  $('.datetime').datetimepicker({
    pickDate: true,
    pickTime: true
  });
  $('.time').datetimepicker({
    pickDate: false
  });
  $('button[id^=\'button-upload\']').on('click', function() {
    var node = this;
    $('#form-upload').remove();
    $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');
    $('#form-upload input[name=\'file\']').trigger('click');
    if (typeof timer != 'undefined') {
      clearInterval(timer);
    }
    timer = setInterval(function() {
      if ($('#form-upload input[name=\'file\']').val() != '') {
        clearInterval(timer);
        $.ajax({
          url: 'index.php?route=tool/upload',
          type: 'post',
          dataType: 'json',
          data: new FormData($('#form-upload')[0]),
          cache: false,
          contentType: false,
          processData: false,
          beforeSend: function() {
            $(node).button('loading');
          },
          complete: function() {
            $(node).button('reset');
          },
          success: function(json) {
            $('.text-danger').remove();
            if (json.error) {
              $(node).parent().find('input').after('<div class="text-danger">' + json.error + '</div>');
            }
            if (json.success) {
              alert(json.success);
              $(node).parent().find('input').val(json.code);
            }
          }
        });
      }
    }, 500);
  });
}

function pqvReplaceButton() {
  if (window.productQuickView && window.productQuickView.replace_button) {
    $("#pqv-button-cart").text(window.productQuickView.btn_in_cart);
    $("#pqv-button-cart").addClass("pqv-btn-in-cart");
  }
}

function pqvAddToCart(product_id, close_if_success) {
  $.ajax({
    url: 'index.php?route=checkout/cart/add',
    type: 'post',
    data: $('#pqv_product input[type=\'text\'], #pqv_product input[type=\'hidden\'], #pqv_product input[type=\'radio\']:checked, #pqv_product input[type=\'checkbox\']:checked, #pqv_product select, #pqv_product textarea'),
    dataType: 'json',
    beforeSend: function() {
      $("#pqv-button-cart").button("loading");
    },
    complete: function() {
      $("#pqv-button-cart").button("reset");
    },
    success: function(json) {
      $('.pqv-alert, .pqv-body .text-danger').remove();
      $('.pqv-body .form-group').removeClass('has-error');
      if (json.error) {
        if (json.error.option) {
          for (var i in json.error.option) {
            var element = $('#input-option' + i.replace('_', '-'));
            if (element.parent().hasClass('input-group')) {
              element.parent().after('<div class="text-danger">' + json.error.option[i] + '</div>');
            } else {
              element.after('<div class="text-danger">' + json.error.option[i] + '</div>');
            }
          }
        }
        if (json.error.recurring) {
          $('select[name=\'recurring_id\']').after('<div class="text-danger">' + json.error.recurring + '</div>');
        }
        $('.pqv-body .text-danger').parent().addClass('has-error');

        var pqvErrorPosition = parseInt($('.pqv-body .has-error:first').position().top) + 10;
        var pqvCurrentScrollTop = parseInt($('.pqv-body').scrollTop());

        if (pqvCurrentScrollTop > pqvErrorPosition) {
          $('.pqv-body').animate({
            scrollTop: pqvErrorPosition
          }, 'slow');
        }
      }
      if (json.success) {
        pqvUpdateMiniCart();

        setTimeout(function() {
          pqvReplaceButton();
        }, 100);

        // Add to cart button change (replace button)
        if (window.abcData &&
          window.abcData.replace_button_cp === '1' &&
          typeof abcReplaceButton === 'function'
        ) {
          abcReplaceButton(product_id);
        }

        // Advanced Pop-up Cart (replace button)
        if (window.apc && typeof window.apc.ReplaceButton === 'function') {
          apc.ReplaceButton(product_id);
        }

        if (close_if_success) {
          pqvClose();
          setTimeout(function() {
            // Add to cart button change (show notification)
            if (window.abcData && typeof abcNotify === 'function') {
              abcNotify(json.success, 'success');
              return;
            }

            // Advanced Pop-up Cart (open pop-up cart)
            if (window.apc && typeof window.apc.OpenPopupCart === 'function') {
              apc.OpenPopupCart("autoclose");
              return;
            }

            // Default Notification
            $('#content').parent().before('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json.success + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            $('html, body').animate({
              scrollTop: 0
            }, 'slow');
          }, 300);
        } else {
          $('.pqv-body').animate({
            scrollTop: 0
          }, 'slow');
          $('#pqv-content').parent().before('<div class="alert alert-success pqv-alert"><i class="fa fa-check-circle"></i> ' + json.success + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        }
      }
    }
  });
}

function pqvAddToCartRelated(product_id, quantity) {
  $.ajax({
    url: 'index.php?route=checkout/cart/add',
    type: 'post',
    data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
    dataType: 'json',
    success: function(json) {
      if (json.redirect) {
        location = json.redirect;
      }
      if (json.success) {
        $('.pqv-alert').remove();
        $('#pqv-content').parent().before('<div class="alert alert-success pqv-alert"><i class="fa fa-check-circle"></i> ' + json.success + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

        pqvUpdateMiniCart();

        // Add to cart button change (replace button)
        if (window.abcData &&
          window.abcData.replace_button_cp === '1' &&
          typeof abcReplaceButton === 'function'
        ) {
          abcReplaceButton(product_id);
        }

        // Advanced Pop-up Cart (replace button)
        if (window.apc && typeof window.apc.ReplaceButton === 'function') {
          apc.ReplaceButton(product_id);
        }
      }
    }
  });
}

function pqvWishlist(product_id) {
  $.ajax({
    url: 'index.php?route=account/wishlist/add',
    type: 'post',
    data: 'product_id=' + product_id,
    dataType: 'json',
    beforeSend: function() {
      $(".tooltip").hide();
    },
    success: function(json) {
      $('.pqv-alert').remove();
      if (json.redirect) {
        location = json.redirect;
      }
      if (json.success) {
        $('#pqv-content').parent().before('<div class="alert alert-info pqv-alert"><i class="fa fa-exclamation-circle"></i> ' + json.success + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
      if (json.info) {
        $('#pqv-content').parent().before('<div class="alert alert-info pqv-alert"><i class="fa fa-info-circle"></i> ' + json.info + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
      }
      $('#wishlist-total span').html(json.total);
      $('#wishlist-total').attr('title', json.total);
    }
  });
}

function pqvCompare(product_id) {
  $.ajax({
    url: 'index.php?route=product/compare/add',
    type: 'post',
    data: 'product_id=' + product_id,
    dataType: 'json',
    beforeSend: function() {
      $('.tooltip').hide();
    },
    success: function(json) {
      $('.pqv-alert').remove();
      if (json.success) {
        $('#pqv-content').parent().before('<div class="alert alert-success pqv-alert"><i class="fa fa-check-circle"></i> ' + json.success + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
        $('#compare-total').html(json.total);
      }
    }
  });
}

function pqvReviewsInit(product_id) {
  $('#pqv-review').delegate('.pagination a', 'click', function(e) {
    e.preventDefault();
    $('#pqv-review').fadeOut('slow');
    $('#pqv-review').load(this.href);
    $('#pqv-review').fadeIn('slow');
  });
  $('#pqv-review').load('index.php?route=product/product/review&product_id=' + product_id);
}

/*  Zoom 1.7.21 license: MIT http://www.jacklmoore.com/zoom */
(function($) {
  var defaults = {
    url: false,
    callback: false,
    target: false,
    duration: 200,
    on: 'mouseover',
    touch: true,
    onZoomIn: false,
    onZoomOut: false,
    magnify: 1
  };

  $.zoom = function(target, source, img, magnify) {
    var targetHeight,
      targetWidth,
      sourceHeight,
      sourceWidth,
      xRatio,
      yRatio,
      offset,
      $target = $(target),
      position = $target.css('position'),
      $source = $(source);

    target.style.position = /(absolute|fixed)/.test(position) ? position : 'relative';
    target.style.overflow = 'hidden';
    img.style.width = img.style.height = '';

    $(img)
      .addClass('zoomImg')
      .css({
        position: 'absolute',
        top: 0,
        left: 0,
        opacity: 0,
        width: img.width * magnify,
        height: img.height * magnify,
        border: 'none',
        maxWidth: 'none',
        maxHeight: 'none'
      })
      .appendTo(target);

    return {
      init: function() {
        targetWidth = $target.outerWidth();
        targetHeight = $target.outerHeight();

        if (source === target) {
          sourceWidth = targetWidth;
          sourceHeight = targetHeight;
        } else {
          sourceWidth = $source.outerWidth();
          sourceHeight = $source.outerHeight();
        }

        xRatio = (img.width - targetWidth) / sourceWidth;
        yRatio = (img.height - targetHeight) / sourceHeight;
        offset = $source.offset();
      },
      move: function(e) {
        var left = (e.pageX - offset.left),
          top = (e.pageY - offset.top);

        top = Math.max(Math.min(top, sourceHeight), 0);
        left = Math.max(Math.min(left, sourceWidth), 0);

        img.style.left = (left * -xRatio) + 'px';
        img.style.top = (top * -yRatio) + 'px';
      }
    };
  };

  $.fn.zoom = function(options) {
    return this.each(function() {
      var
        settings = $.extend({}, defaults, options || {}),
        target = settings.target && $(settings.target)[0] || this,
        source = this,
        $source = $(source),
        img = document.createElement('img'),
        $img = $(img),
        mousemove = 'mousemove.zoom',
        clicked = false,
        touched = false;

      if (!settings.url) {
        var srcElement = source.querySelector('img');
        if (srcElement) {
          settings.url = srcElement.getAttribute('data-src') || srcElement.currentSrc || srcElement.src;
        }
        if (!settings.url) {
          return;
        }
      }

      $source.one('zoom.destroy', function(position, overflow) {
        $source.off(".zoom");
        target.style.position = position;
        target.style.overflow = overflow;
        img.onload = null;
        $img.remove();
      }.bind(this, target.style.position, target.style.overflow));

      img.onload = function() {
        var zoom = $.zoom(target, source, img, settings.magnify);

        function start(e) {
          zoom.init();
          zoom.move(e);
          $img.stop()
            .fadeTo(settings.duration, 1, $.isFunction(settings.onZoomIn) ? settings.onZoomIn.call(img) : false);
        }

        function stop() {
          $img.stop()
            .fadeTo(settings.duration, 0, $.isFunction(settings.onZoomOut) ? settings.onZoomOut.call(img) : false);
        }

        if (settings.on === 'mouseover') {
          zoom.init();
          $source
            .on('mouseenter.zoom', start)
            .on('mouseleave.zoom', stop)
            .on(mousemove, zoom.move);
        }

        if (settings.touch) {
          $source
            .on('touchstart.zoom', function(e) {
              e.preventDefault();
              if (touched) {
                touched = false;
                stop();
              } else {
                touched = true;
                start(e.originalEvent.touches[0] || e.originalEvent.changedTouches[0]);
              }
            })
            .on('touchmove.zoom', function(e) {
              e.preventDefault();
              zoom.move(e.originalEvent.touches[0] || e.originalEvent.changedTouches[0]);
            })
            .on('touchend.zoom', function(e) {
              e.preventDefault();
              if (touched) {
                touched = false;
                stop();
              }
            });
        }

        if ($.isFunction(settings.callback)) {
          settings.callback.call(img);
        }
      };

      img.setAttribute('role', 'presentation');
      img.alt = '';
      img.src = settings.url;
    });
  };
  $.fn.zoom.defaults = defaults;
}(window.jQuery));
