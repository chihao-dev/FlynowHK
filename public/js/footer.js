  document.addEventListener('DOMContentLoaded', function(){

    const airlineSwiper = new Swiper('.airline-logo-slider', {

        loop: true,

        slidesPerView: 7,

        spaceBetween: 20,

        autoplay: {

            delay: 2000,

            disableOnInteraction: false,

        },

        breakpoints: {

        }

    });




    airlineSwiper.update();


});

  const swiper = new Swiper('.banner-slider', {

    loop: true,

    autoplay: {

      delay: 2500,

      disableOnInteraction: false,

    },

    pagination: {

      el: '.swiper-pagination',

      clickable: true,

    },

  });