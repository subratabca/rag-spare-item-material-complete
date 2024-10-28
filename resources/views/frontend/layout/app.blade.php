<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-wide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('frontend/assets/') }}"
  data-template="front-pages">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title')</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('frontend/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

   <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/fonts/materialdesignicons.css') }}" />
    <!-- Ext Icons-->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/fonts/flag-icons.css') }}" /> 

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/css/rtl/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/css/rtl/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/css/pages/front-page.css') }}" />


    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/nouislider/nouislider.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/swiper/swiper.css') }}" />
    <!-- Ext Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/quill/typography.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/quill/katex.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/quill/editor.css') }}" />

    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" /> 
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css') }}" /> 

    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css') }}" />

    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css') }}" 
    />

    @stack('styles')

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/css/pages/front-page-landing.css') }}" />
    <!-- Ext Page CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/vendor/css/pages/page-profile.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('frontend/assets/vendor/js/helpers.js') }}"></script>

    <!-- <script src="{{ asset('frontend/assets/vendor/js/template-customizer.js') }}"></script> -->

    <!-- <script src="{{ asset('frontend/assets/js/front-config.js') }}"></script> -->

    <script src="{{ asset('backend/custom-js/axios.min.js') }}"></script>
    <link href="{{ asset('backend/custom-css/toastify.min.css') }}" rel="stylesheet" />
    <script src="{{asset('backend/custom-js/toastify-js.js')}}"></script>
    <script src="{{asset('backend/custom-js/config.js')}}"></script>
  </head>

  <body>
    <script src="{{ asset('frontend/assets/vendor/js/dropdown-hover.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/js/mega-dropdown.js') }}"></script>

    <div id="bouncing-loader" class="loading-spinner" style="display: none;">
      <div class="spinner-container">
        <div class="spinner-circle"></div>
        <div class="spinner-circle"></div>
        <div class="spinner-circle"></div>
      </div>
    </div>
    
      @include('frontend.layout.header')

      <div data-bs-spy="scroll" class="scrollspy-example">

      @yield('content')

      </div>

      @include('frontend.layout.footer')

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('frontend/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="{{ asset('frontend/assets/vendor/libs/nouislider/nouislider.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/libs/swiper/swiper.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/libs/quill/quill.js') }}"></script>

    <script src="{{ asset('frontend/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>

    @stack('scripts')

    <!-- Main JS -->
    <script src="{{ asset('frontend/assets/js/front-main.js') }}"></script>

    <!-- Page JS -->
    <script src="{{ asset('frontend/assets/js/front-page-landing.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/tables-datatables-basic.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/tables-datatables-advanced.js') }}"></script>

  </body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        settingInfo(); 
    });

    async function settingInfo() {
      showLoader();
      try {
          const response = await axios.get('/setting-list');

          if (response.status === 200) {
              const data = response.data.data;
              document.getElementById('logo').src = data['logo'] ? "/upload/site-setting/" + data['logo'] : "/upload/no_image.jpg";
          }
      } catch (error) {
          handleError(error);
      }finally{
          hideLoader();
      }
    }


    function handleError(error) {
      if (error.response) {
          const status = error.response.status;
          const message = error.response.data.message || 'An unexpected error occurred';
          if (status === 500) {
              errorToast(message || 'Server Error');
          } else {
              errorToast(message);
          }
      } else {
          errorToast('No response received from the server.');
      }
    }
</script>