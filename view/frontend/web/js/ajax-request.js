define([
    'jquery',
    'mage/mage',
    'mage/validation'
], function ($) {
    'use strict';
    return function(config) {
        $('#products_in_range_form').on('submit', function(e) {
            e.preventDefault();

            // Validate form
            if (!$('#products_in_range_form').validation()
                || !$('#products_in_range_form').validation('isValid')
            ) {
              return false;
            }

            // Load grid data
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: $(this).serialize(),
                contentType: 'application/x-www-form-urlencoded',
                showLoader: true,
                beforeSend: function() {
                  $('#validation_errors').empty();
                  $('#products-in-range-table tbody').empty();
                }
            }).done(function(response) {
                if (response.hasOwnProperty('error')) {
                    $('#validation_errors').append('<p>' + response.error + '</p>');
                }

                if (response.length > 0) {
                  $('.table-wrapper.products-in-range').show();
                  $('#no_products_message').hide();
                  // Append data to table element
                  let template = '<tr>\
                      <td data-th="Thumbnail" class="col thumbnail">\
                          <img src="%1" name="%3" alt="%3" style="max-width:80px" />\
                      </td>\
                      <td data-th="SKU" class="col sku">%2</td>\
                      <td data-th="Name" class="col name">%3</td>\
                      <td data-th="Qty In Stock" class="col qty">%4</td>\
                      <td data-th="Price" class="col price">\
                       %5\
                      </td>\
                      <td data-th="Special Price" class="col final_price">\
                      %6\
                      </td>\
                      <td data-th="Link" class="col link">\
                          <a href="%7" class="action view" target="_blank">\
                              <span>View Product</span>\
                          </a>\
                      </td>\
                  </tr>';
                  $.each(response, function(i, data) {
                    $('#products-in-range-table tbody').append(
                      template.replace('%1', data.thumbnail)
                        .replace('%2', data.sku)
                        .replace(/\%3/g, data.name)
                        .replace('%4', data.qty)
                        .replace('%5', data.price)
                          .replace('%6', data.final_price)
                        .replace('%7', data.url)
                    );
                  });
                } else {
                  $('.table-wrapper.products-in-range').hide();
                  $('#no_products_message').show();
                }
            });

          });
    }

});
