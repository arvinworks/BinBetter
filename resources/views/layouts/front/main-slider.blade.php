  <!--Main Slider Start-->
  <section class="main-slider main-slider-three">
      <div class="swiper-container thm-swiper__slider" data-swiper-options='{"slidesPerView": 1, "loop": false,
                "effect": "fade",
                "pagination": {
                "el": "#main-slider-pagination",
                "type": "bullets",
                "clickable": true
                },
                "navigation": {
                "nextEl": "#main-slider__swiper-button-next",
                "prevEl": "#main-slider__swiper-button-prev"
                },
                "autoplay": {
                "delay": 5000
                }}'>
          <div class="swiper-wrapper">

              <div class="swiper-slide">
                  <div class="main-slider-three-bg"
                      style="background-image: url("{{ asset('assets/front/images/backgrounds/main-slider-3-bg.jpg') }}");">
                  </div>
                  <div class="main-slider-three-bg-two"></div>
                  <div class="main-slider-three-building"><img src="{{ asset('assets/front/images/resources/main-slider-three-building.png' ) }}" alt=""></div>
                  <div class="main-slider-three-car"><img src="{{ asset('assets/front/images/resources/main-slider-three-car.png' ) }}"
                          alt="" class="float-bob-y"></div>
                  <div class="container">
                      <div class="row">
                          <div class="col-xl-7">
                              <div class="main-slider__content">
                                  <h2>Reliable <br> & Affordable <br> Waste Services</h2>

                @if (Route::has('login'))
                @auth
                <a href="{{ url('/home') }}" class="thm-btn">Home</a>
                @else
                <a href="{{ route('login') }}" class="thm-btn manage-waste__btn-1">Login</a>
                @if (Route::has('register'))
                <a href="{{ route('register') }}" class="thm-btn">Register</a>
                @endif
                @endauth
                @endif

                                  
                                 
                              </div>
                          </div>
                      </div>
                  </div>
              </div>

          </div>

          <!-- If we need navigation buttons -->

      </div>
  </section>
  <!--Main Slider End-->