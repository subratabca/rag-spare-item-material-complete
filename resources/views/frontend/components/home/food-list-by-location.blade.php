<section id="landingFeatures" class="section-py landing-features">
  <div class="container">               
    <div class="app-academy">

        <div class="card mb-4">
          <div class="card-header d-flex flex-wrap justify-content-between gap-3">
            <div class="card-title mb-0 me-1">
              <h5 class="mb-1">Food List By Your Location</h5>
              <p class="mb-0">Total 6 courses you have purchased</p>
            </div>

            <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
              <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                <input type="search" id="search-by-location" name="search-by-location" placeholder="Find your food" class="form-control me-2" />
                <button type="submit" class="btn btn-primary btn-icon" aria-label="Search"><i class="mdi mdi-magnify"></i></button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <!-- Hidden inputs for latitude and longitude -->
            <input type="hidden" id="latitude" value="">
            <input type="hidden" id="longitude" value="">

            <div class="row gy-4 mb-4" id="food-list-by-location">
              <!-- Food list by location will be inserted here -->
            </div>
            <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
              <ul class="pagination-by-location">
                <!-- Pagination links will be inserted here -->
              </ul>
            </nav>
          </div>
        </div>
    </div>
  </div>
</section>


<script>
  let debounceTimeoutLocation;

  function debounceLocation(func, delay) {
      return function(...args) {
          clearTimeout(debounceTimeoutLocation);
          debounceTimeoutLocation = setTimeout(() => func.apply(this, args), delay);
      };
  }

  async function FoodListByLocation(page = 1, searchQuery = null, latitude = null, longitude = null) {
      try {
          let url = `/food-list-by-location?page=${page}`;

          if (searchQuery) {
              url += `&search-by-location=${searchQuery}`;
          }

          // Only add latitude and longitude if available
          if (latitude && longitude) {
              url += `&latitude=${latitude}&longitude=${longitude}`;
          }

          const res = await axios.get(url);
          const data = res.data;

          // Check if the response indicates success and food items
          if (data.status === 'success') {
              if (data.hasFood) {
                  updateFoodListByLocation(data.foods.data);
                  updatePaginationByLocation(data.foods);
              } else {
                  displaySearchLocationError(data.message);
              }
              return data.foods.data; // Return food list for condition in home.blade.php
          } else {
              displaySearchLocationError(data.message);
              return []; // Return empty array if no food is found
          }
      } catch (error) {
          handleLocationError(error);
          return []; // Return empty array on error
      }
  }

  function updateFoodListByLocation(foodData) {
      const foodListByLocation = document.getElementById('food-list-by-location');
      foodListByLocation.innerHTML = foodData.map(food => {
          return `
              <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6">
                  <div class="product-box1">
                      <a href="/food-details/${food.id}">
                          <img class="img-responsive" src="/upload/food/medium/${food.image}" alt="${food.name}">
                      </a>
                      <div class="product-content-holder">
                          <h3><a href="/food-details/${food.id}">${food.name}</a></h3>
                          <span><strong>Collection Address:</strong> ${food.address}</span>
                      </div>
                  </div>
              </div>
          `;
      }).join('');
  }

  function updatePaginationByLocation(paginationData) {
      const paginationContainer = document.querySelector('.pagination-by-location');
      paginationContainer.innerHTML = '';

      if (paginationData.prev_page_url) {
          paginationContainer.innerHTML += `<li><a href="${paginationData.prev_page_url}" onclick="return loadPageLocation(event, '${paginationData.prev_page_url}')">Previous</a></li>`;
      }

      paginationData.links.forEach(link => {
          if (link.active) {
              paginationContainer.innerHTML += `<li><a href="#" class="active">${link.label}</a></li>`;
          } else if (link.url) {
              paginationContainer.innerHTML += `<li><a href="${link.url}" onclick="return loadPageLocation(event, '${link.url}')">${link.label}</a></li>`;
          }
      });

      if (paginationData.next_page_url) {
          paginationContainer.innerHTML += `<li><a href="${paginationData.next_page_url}" onclick="return loadPageLocation(event, '${paginationData.next_page_url}')">Next</a></li>`;
      }
  }

  function displaySearchLocationError(message) {
      const errorContainer = document.getElementById('search-error');
      errorContainer.textContent = message;
  }

  function handleLocationError(error) {
      if (error.response) {
          displaySearchLocationError(error.response.data.message || 'Food not found.');
      } else {
          displaySearchLocationError('Failed to connect to the server. Please try again.');
      }
  }

  function loadPageLocation(event, url) {
      event.preventDefault();
      const searchQuery = document.querySelector('input[name="search-by-location"]').value;
      const page = new URL(url).searchParams.get('page');
      const latitude = document.getElementById('latitude').value; // Assume you have hidden fields for latitude and longitude
      const longitude = document.getElementById('longitude').value;

      FoodListByLocation(page, searchQuery, latitude, longitude);
  }

  const debouncedSearchLocation = debounceLocation(function() {
      const searchQuery = document.querySelector('input[name="search-by-location"]').value;
      const latitude = document.getElementById('latitude').value;
      const longitude = document.getElementById('longitude').value;

      FoodListByLocation(1, searchQuery, latitude, longitude); // Fetch new search results
  }, 500);

  document.getElementById('search-by-location').addEventListener('input', debouncedSearchLocation);

document.addEventListener('DOMContentLoaded', function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;

            console.log('Latitude:', latitude, 'Longitude:', longitude); // Log the coordinates

            // Save latitude and longitude to hidden input fields
            document.getElementById('latitude').value = latitude;
            document.getElementById('longitude').value = longitude;

            FoodListByLocation(1, null, latitude, longitude);
        }, (error) => {
            console.error('Geolocation error:', error); // Log error
            FoodListByLocation(1); // Load without location if user denies access
        });
    } else {
        FoodListByLocation(1); // Load without location if geolocation not available
    }
});

</script>

