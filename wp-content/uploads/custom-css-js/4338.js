/******* Do not edit this file *******
Simple Custom CSS and JS - by Silkypress.com
Saved: Jul 12 2018 | 07:39:14 */
jQuery(document).ready(function(){
   jQuery("li.woocommerce-MyAccount-navigation-link--dashboard").removeClass("is-active");
   jQuery("p.order-again").appendTo("section.woocommerce-customer-details");
   jQuery("nav.woocommerce-MyAccount-navigation ul li.woocommerce-MyAccount-navigation-link--orders").appendTo("div#learndash_profile .profile_info");
   jQuery("li.woocommerce-MyAccount-navigation-link--orders").attr("id","order-active");
  

  function hasClass(element, cls) {
      return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
  }

  var el = document.getElementById('order-active');
  if(hasClass(el, 'is-active')){
  	jQuery("div.woocommerce-MyAccount-content").css("display", "block");
  }

});