function getURLVar(key) {
    var value = [];

    var query = String(document.location).split('?');

    if (query[1]) {
        var part = query[1].split('&');

        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');

            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }

        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
}

// $(window).on('load', function () {
//         setTimeout(function () {
//             $("body").css("overflow", "auto");
//             $(".loader-over-lay").hide()
//         }, 2000)
//     });


$(window).on('load', function () {
        setTimeout(function () {
            $("body").css("overflow", "auto");
            $(".loader-over-lay").hide()
        }, 2000)
    });


// $(document).ready(function () {
//     var fluid = function() {
//         var $theWindowSize = $(this).width();
// if($theWindowSize < 768)
//     {
       
// $('.head-list-mob').prepend($('.dark-mode'));

//     } else {
       
//        $('.head-list-desk').prepend($('.dark-mode'));

//     }
//     };

//     // Fire on DOM ready
//     fluid();

//     // Fire upon resize
//     $(window).resize(fluid);
// });


$(document).ready(function () {

var element = jQuery('#cart .red').clone();
element.appendTo('.cart2'); 

    //menu
    $("#mp-menu").pushMenu({
        type: "overlap",
        levelSpacing: 0,
        backClass: "mp-back",
        trigger: ".burger",
        pusher: ".site-outer",
        scrollTop: !1
    });


$('.product-thumb h4 ').matchHeight();  
$('.product-thumb .p-cat ').matchHeight();  
$('.product-thumb .caption-cont ').matchHeight(); 
$('.featuredbanner .exc h3 ').matchHeight();  
$('.featuredbanner .exc h4 ').matchHeight();  
$('.refine-item + a ').matchHeight();  

    $(".dark-mode").click(function () {
        $(this).toggleClass('active');
        $("body").toggleClass('dark-theme');
        if ($(this).hasClass('active')) {
            localStorage.setItem("mode", "dark");
        } else {
            localStorage.setItem("mode", "light");
        }
        console.log(localStorage.mode)
    });

    /*============= if ================*/
    if (localStorage.mode == "dark") {
        $("body").addClass('dark-theme');
        $(".dark-mode").addClass('active')
    };



    //search pop
    jQuery('.open-search').on('click', function (event) {
        event.preventDefault();
        jQuery('#search-header').addClass('open');
    });
    jQuery('#search-header, #search-header button.close').on('click keyup', function (event) {
        if (event.target == this || event.target.className == 'close' || event.keyCode == 27) {
            jQuery(this).removeClass('open');
        }
    });

    /* main Search */
    $('header #search-header .search input[name=\'search\']').parent().find('button').on('click', function () {
        url = $('base').attr('href') + 'index.php?route=product/search';

        var value = $('header #search-header input[name=\'search\']').val();

        if (value) {
            url += '&search=' + encodeURIComponent(value);
        }

        location = url;
    });
    $('header #search-header .search input[name=\'search\']').on('keydown', function (e) {
        if (e.keyCode == 13) {
            $('header #search-header input[name=\'search\']').parent().find('button').trigger('click');
        }
    });


//search +category
    $('.adv-search #button-search').bind('click', function () {
        url = 'index.php?route=product/search';

        var search = $('.adv-search input[name=\'search\']').prop('value');

        if (search) {
            url += '&search=' + encodeURIComponent(search);
        }

        var category_id = $('.adv-search select[name=\'category_id\']').prop('value');

        if (category_id > 0) {
            url += '&category_id=' + encodeURIComponent(category_id);
        }

        var sub_category = $('.adv-search input[name=\'sub_category\']:checked').prop('value');

        if (sub_category) {
            url += '&sub_category=true';
        }

        var filter_description = $('.adv-search input[name=\'description\']:checked').prop('value');

        if (filter_description) {
            url += '&description=true';
        }

        location = url;
    });

    $(' .adv-search input[name=\'search\']').bind('keydown', function (e) {
        if (e.keyCode == 13) {
            $('.adv-search #button-search').trigger('click');
        }
    });

    $('.adv-search select[name=\'category_id\']').on('change', function () {
        if (this.value == '0') {
            $('.adv-search input[name=\'sub_category\']').prop('disabled', true);
        } else {
            $('.adv-search input[name=\'sub_category\']').prop('disabled', false);
        }
    });

    $('.adv-search select[name=\'category_id\']').trigger('change');


    // Highlight any found errors
    $('.text-danger').each(function () {
        var element = $(this).parent().parent();

        if (element.hasClass('form-group')) {
            element.addClass('has-error');
        }
    });

    // Currency
    $('#form-currency .currency-select').on('click', function (e) {
        e.preventDefault();

        $('#form-currency input[name=\'code\']').val($(this).attr('name'));

        $('#form-currency').submit();
    });

    // Language
    $('#form-language .language-select').on('click', function (e) {
        e.preventDefault();

        $('#form-language input[name=\'code\']').val($(this).attr('name'));

        $('#form-language').submit();
    });


    // Menu
    $('#menu .dropdown-menu').each(function () {
        var menu = $('#menu').offset();
        var dropdown = $(this).parent().offset();

        var i = (dropdown.left + $(this).outerWidth()) - (menu.left + $('#menu').outerWidth());

        if (i > 0) {
            $(this).css('margin-left', '-' + (i + 10) + 'px');
        }
    });

    // Product List
    $('#list-view').click(function () {
        $('#content .product-grid > .clearfix').remove();

        $('#content  .product-grid').attr('class', 'product-layout product-list col-xs-12');
        $('#grid-view').removeClass('active');
        $('#list-view').addClass('active');

        localStorage.setItem('display', 'list');
    });

    // Product Grid
    $('#grid-view').click(function () {
        // What a shame bootstrap does not take into account dynamically loaded columns
        var cols = $('#column-right, #column-left').length;

        if (cols == 2) {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-6 col-md-6 col-sm-12 col-xs-6');
        } else if (cols == 1) {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-4 col-md-4 col-sm-6 col-xs-6');
        } else {
            $('#content .product-list').attr('class', 'product-layout product-grid col-lg-3 col-md-3 col-sm-6 col-xs-6');
        }

        $('#list-view').removeClass('active');
        $('#grid-view').addClass('active');

        localStorage.setItem('display', 'grid');
    });

    if (localStorage.getItem('display') == 'list') {
        $('#list-view').trigger('click');
        $('#list-view').addClass('active');
    } else {
        $('#grid-view').trigger('click');
        $('#grid-view').addClass('active');
    }


    // Checkout
    $(document).on('keydown', '#collapse-checkout-option input[name=\'email\'], #collapse-checkout-option input[name=\'password\']', function (e) {
        if (e.keyCode == 13) {
            $('#collapse-checkout-option #button-login').trigger('click');
        }
    });

    // tooltips on hover
    $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});

    // Makes tooltips work on ajax generated content
    $(document).ajaxStop(function () {
        $('[data-toggle=\'tooltip\']').tooltip({container: 'body'});
    });
});


// Cart add remove functions
var cart = {
    'add': function (product_id, quantity) {
        $.ajax({
            url: 'index.php?route=checkout/cart/add',
            type: 'post',
            data: 'product_id=' + product_id + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',
            beforeSend: function () {
                $('#cart > button').button('loading');
            },
            complete: function () {
                $('#cart > button').button('reset');
            },
            success: function (json) {
                $('.alert-dismissible, .text-danger').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {

                    if (document.dir == "rtl") {
                        var success = "موافق"
                    } else {
                        var success = "OK"
                    }

                    swal({
                        title: " ",
                        html: json['success'],
                        type: "success",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-success',
                        confirmButtonText: success
                    });
                    // Need to set timeout otherwise it wont update the total
                    setTimeout(function () {
                        $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span> <span class="hidden-xs hidden-sm hidden-md text_cart"> ' + json['text_cart'] + '</span>' );
                  
var element = jQuery('#cart .red').clone();
element.appendTo('.cart2'); 
                    }, 100);

                    // $('html, body').animate({ scrollTop: 0 }, 'slow');

                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'update': function (key, quantity) {
        $.ajax({
            url: 'index.php?route=checkout/cart/edit',
            type: 'post',
            data: 'key=' + key + '&quantity=' + (typeof (quantity) != 'undefined' ? quantity : 1),
            dataType: 'json',
            beforeSend: function () {
                $('#cart > button').button('loading');
            },
            complete: function () {
                $('#cart > button').button('reset');
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] +  '</span> <span class="hidden-xs hidden-sm hidden-md text_cart"> ' + json['text_cart'] + '</span>' );
               
var element = jQuery('#cart .red').clone();
element.appendTo('.cart2'); 
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function (key) {
        $.ajax({
            url: 'index.php?route=checkout/cart/remove',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',
            beforeSend: function () {
                $('#cart > button').button('loading');
            },
            complete: function () {
                $('#cart > button').button('reset');
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] +  '</span> <span class="hidden-xs hidden-sm hidden-md text_cart"> ' + json['text_cart'] + '</span>' );
               
var element = jQuery('#cart .red').clone();
element.appendTo('.cart2'); 
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}

var voucher = {
    'add': function () {

    },
    'remove': function (key) {
        $.ajax({
            url: 'index.php?route=checkout/cart/remove',
            type: 'post',
            data: 'key=' + key,
            dataType: 'json',
            beforeSend: function () {
                $('#cart > button').button('loading');
            },
            complete: function () {
                $('#cart > button').button('reset');
            },
            success: function (json) {
                // Need to set timeout otherwise it wont update the total
                setTimeout(function () {
                    $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');
                }, 100);

                if (getURLVar('route') == 'checkout/cart' || getURLVar('route') == 'checkout/checkout') {
                    location = 'index.php?route=checkout/cart';
                } else {
                    $('#cart > ul').load('index.php?route=common/cart/info ul li');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }
}

var wishlist = {
    'add': function (product_id) {
        $.ajax({
            url: 'index.php?route=account/wishlist/add',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function (json) {
                $('.alert-dismissible').remove();

                if (json['redirect']) {
                    location = json['redirect'];
                }

                if (json['success']) {
                    //$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    if (document.dir == "rtl") {
                        var success = "موافق"
                    } else {
                        var success = "OK"
                    }

                    swal({
                        title: " ",
                        html: json['success'],
                        type: "success",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-success',
                        confirmButtonText: success
                    });

                }
// $('.red2').replaceWith( '<span class="red2">' + json['total'] + '</span>');
                $('#wishlist-total span').html(json['total']);
                $('#wishlist-total').attr('title', json['total']);

                // $('html, body').animate({ scrollTop: 0 }, 'slow');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function () {

    }
}

var compare = {
    'add': function (product_id) {
        $.ajax({
            url: 'index.php?route=product/compare/add',
            type: 'post',
            data: 'product_id=' + product_id,
            dataType: 'json',
            success: function (json) {
                $('.alert-dismissible').remove();

                if (json['success']) {
                    //$('#content').parent().before('<div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    if (document.dir == "rtl") {
                        var success = "موافق"
                    } else {
                        var success = "OK"
                    }

                    swal({
                        title: " ",
                        html: json['success'],
                        type: "success",
                        showCancelButton: false,
                        confirmButtonClass: 'btn-success',
                        confirmButtonText: success
                    });
                    $('#compare-total').html(json['total']);

                    // $('html, body').animate({ scrollTop: 0 }, 'slow');
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    },
    'remove': function () {

    }
}

/* Agree to Terms */
$(document).delegate('.agree', 'click', function (e) {
    e.preventDefault();

    $('#modal-agree').remove();

    var element = this;

    $.ajax({
        url: $(element).attr('href'),
        type: 'get',
        dataType: 'html',
        success: function (data) {
            html = '<div id="modal-agree" class="modal">';
            html += '  <div class="modal-dialog">';
            html += '    <div class="modal-content">';
            html += '      <div class="modal-header">';
            html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
            html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
            html += '      </div>';
            html += '      <div class="modal-body">' + data + '</div>';
            html += '    </div>';
            html += '  </div>';
            html += '</div>';

            $('body').append(html);

            $('#modal-agree').modal('show');
        }
    });

});

$('footer .collapse').on('show.bs.collapse', function () {
    $(this).parent('.content-footer [class^="col"]').find('.fa-minus').show();
    $(this).parent('.content-footer [class^="col"] ').find('.fa-plus').hide();
});
$('footer .collapse').on('hide.bs.collapse', function () {
    $(this).parent('.content-footer [class^="col"] ').find('.fa-minus').hide();
    $(this).parent('.content-footer [class^="col"] ').find('.fa-plus').show();
});

$('.panel-collapse').on('show.bs.collapse', function () {
    $(this).parent('.panel').find('.fa-minus-square-o').show();
    $(this).parent('.panel').find('.fa-plus-square-o').hide();
});
$('.panel-collapse').on('hide.bs.collapse', function () {
    $(this).parent('.panel').find('.fa-minus-square-o').hide();
    $(this).parent('.panel').find('.fa-plus-square-o').show();
});


// Autocomplete */
(function ($) {
    $.fn.autocomplete = function (option) {
        return this.each(function () {
            this.timer = null;
            this.items = new Array();

            $.extend(this, option);

            $(this).attr('autocomplete', 'off');

            // Focus
            $(this).on('focus', function () {
                this.request();
            });

            // Blur
            $(this).on('blur', function () {
                setTimeout(function (object) {
                    object.hide();
                }, 200, this);
            });

            // Keydown
            $(this).on('keydown', function (event) {
                switch (event.keyCode) {
                    case 27: // escape
                        this.hide();
                        break;
                    default:
                        this.request();
                        break;
                }
            });

            // Click
            this.click = function (event) {
                event.preventDefault();

                value = $(event.target).parent().attr('data-value');

                if (value && this.items[value]) {
                    this.select(this.items[value]);
                }
            }

            // Show
            this.show = function () {
                var pos = $(this).position();

                $(this).siblings('ul.dropdown-menu').css({
                    top: pos.top + $(this).outerHeight(),
                    left: pos.left
                });

                $(this).siblings('ul.dropdown-menu').show();
            }

            // Hide
            this.hide = function () {
                $(this).siblings('ul.dropdown-menu').hide();
            }

            // Request
            this.request = function () {
                clearTimeout(this.timer);

                this.timer = setTimeout(function (object) {
                    object.source($(object).val(), $.proxy(object.response, object));
                }, 200, this);
            }


            // Response
            this.response = function (json) {
                html = '';

                if (json.length) {
                    for (i = 0; i < json.length; i++) {
                        this.items[json[i]['value']] = json[i];
                    }

                    for (i = 0; i < json.length; i++) {
                        if (!json[i]['category']) {
                            html += '<li data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
                        }
                    }

                    // Get all the ones with a categories
                    var category = new Array();

                    for (i = 0; i < json.length; i++) {
                        if (json[i]['category']) {
                            if (!category[json[i]['category']]) {
                                category[json[i]['category']] = new Array();
                                category[json[i]['category']]['name'] = json[i]['category'];
                                category[json[i]['category']]['item'] = new Array();
                            }

                            category[json[i]['category']]['item'].push(json[i]);
                        }
                    }

                    for (i in category) {
                        html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

                        for (j = 0; j < category[i]['item'].length; j++) {
                            html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
                        }
                    }
                }

                if (html) {
                    this.show();
                } else {
                    this.hide();
                }

                $(this).siblings('ul.dropdown-menu').html(html);
            }

            $(this).after('<ul class="dropdown-menu"></ul>');
            $(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

        });
    }
})(window.jQuery);
