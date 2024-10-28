<section id="landingFeatures" class="section-py landing-features">
  <div class="container">               
    <div class="app-academy">
        <div class="card p-0 mb-4">
          <div class="card-body d-flex flex-column flex-md-row justify-content-between p-0 pt-4">
            <div class="app-academy-md-25 card-body py-0">
              <img
                src="{{ asset('frontend/assets/img/illustrations/bulb-light.png') }}"
                class="img-fluid app-academy-img-height scaleX-n1-rtl"
                alt="Bulb in hand"
                data-app-light-img="illustrations/bulb-light.png"
                data-app-dark-img="illustrations/bulb-dark.png"
                height="90" />
            </div>
            <div
              class="app-academy-md-50 card-body d-flex align-items-md-center flex-column text-md-center mb-4">
              <span class="card-title mb-3 lh-lg px-md-5 display-6 text-heading">
                Education, talents, and career opportunities.
                <span class="text-primary text-nowrap">All in one place</span>.
              </span>
              <p class="mb-3 px-2">
                Grow your skill with the most reliable online courses and certifications in marketing,
                information technology, programming, and data science.
              </p>
<!--               <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                <input type="search" id="search-field1" name="search-field1" value="" placeholder="Find your food" class="form-control me-2" />
                <button type="submit" class="btn btn-primary btn-icon"><i class="mdi mdi-magnify"></i></button>
              </div>
              <div id="search-error1" class="text-danger" style="margin-top: 5px;"></div> -->
            </div>
            <div class="app-academy-md-25 d-flex align-items-end justify-content-end">
              <img
                src="{{ asset('frontend/assets/img/illustrations/pencil-rocket.png') }}"
                alt="pencil rocket"
                height="188"
                class="scaleX-n1-rtl" />
            </div>
          </div>
        </div> 
        <div class="card mb-4">
          <div class="card-header d-flex flex-wrap justify-content-between gap-3">
            <div class="card-title mb-0 me-1">
              <h5 class="mb-1">All Food List</h5>
              <p class="mb-0">Total 6 course you have purchased</p>
            </div>

            <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
              <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                <input type="search" id="search-field" name="search-field" value="" placeholder="Find your food" class="form-control me-2" />
                <button type="submit" class="btn btn-primary btn-icon"><i class="mdi mdi-magnify"></i></button>
              </div>
              <div id="search-error" class="text-danger" style="margin-top: 5px;"></div>
            </div>
          </div>
          <div class="card-body">
            <div class="row gy-4 mb-4" id="gried-view">

            </div>
            <nav aria-label="Page navigation" class="d-flex align-items-center justify-content-center">
              <ul class="pagination">

              </ul>
            </nav>
          </div>
        </div>
    </div>
  </div>
</section>


<script>
let debounceTimeout;

function debounce(func, delay) {
    return function(...args) {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => func.apply(this, args), delay);
    };
}

async function FoodList(page = 1, date = null, searchQuery = null) {
    try {
        let url = `/food-list?page=${page}`;
        if (date) {
            url = `/food-list/date/${date}?page=${page}`;
        }

        if (searchQuery) {
            url = `/search-food?query=${searchQuery}`;  // Use the search endpoint
        }

        const res = await axios.get(url);
        const data = res.data;

        if (searchQuery && data.status === 'success') {
            const foodData = data.foods;
            updateFoodList(foodData);
            clearError();
        } else if (searchQuery && data.status === 'failed') {
            displaySearchError(data.message);
        } else if (data.status === 'success') {
            const foodData = data.foods.data;
            updateFoodList(foodData);
            clearError();
        } else if (data.status === 'failed') {
            displaySearchError(data.message);
        }

        if (!searchQuery) {
            updatePagination(data.foods);
        }

        // Scroll to the food list section after loading the content
        //document.getElementById('gried-view').scrollIntoView({ behavior: 'smooth' });

    } catch (error) {
        handleError(error);
    }
}

function updateFoodList(foodData) {
    const gridViewContainer = document.getElementById('gried-view');
    gridViewContainer.innerHTML = foodData.map(food => {
        const isProcessing = food.status === "processing";
        const disabledStyle = isProcessing ? 'style="pointer-events: none; opacity: 0.5;"' : '';
        const foodName = food.name;
        const requestBadge = isProcessing ? `<span class="btn btn-danger">under request</span>` : '';
        const collectionAddress = !isProcessing ? `<span style='color:green'><strong>Collection Address:</strong></span>` : '';
        const foodAddress = !isProcessing ? `<span><i class="mdi mdi-map-marker me-2"></i>${food.address}</span>` : requestBadge;

        return `
            <div class="col-sm-6 col-lg-4" ${disabledStyle}>
                <div class="card p-2 h-100 shadow-none border">
                    <div class="rounded-2 text-center mb-3">
                        <a href="/food-details/${food.id}" ${disabledStyle}>
                            <img class="img-fluid" src="/upload/food/small/${food.image}" alt="${foodName}">
                        </a>
                    </div>
                    <div class="card-body p-3 pt-2">
                        <a href="/food-details/${food.id}" class="h5" ${disabledStyle}>
                            ${foodName}
                        </a>
                        <p class="mt-2"><strong>Gradients:</strong> ${food.gradients}</p>
                        <p class="d-flex align-items-center">
                           ${foodAddress}
                        </p>
                        <div class="progress rounded-pill mb-4" style="height: 8px">
                            <div class="progress-bar" style="width: ${food.progress}%" role="progressbar" aria-valuenow="${food.progress}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex flex-column flex-md-row gap-3 text-nowrap flex-wrap flex-md-nowrap flex-lg-wrap flex-xxl-nowrap">
                            <a class="w-100 btn btn-outline-secondary d-flex align-items-center" href="/food-details/${food.id}" ${disabledStyle}>
                                <i class="mdi mdi-sync align-middle me-1"></i><span>Start Over</span>
                            </a>
                            <a class="w-100 btn btn-outline-primary d-flex align-items-center" href="/food-details/${food.id}" ${disabledStyle}>
                                <span class="me-1">Continue</span><i class="mdi mdi-arrow-right lh-1 scaleX-n1-rtl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

function updatePagination(paginationData) {
    const paginationContainer = document.querySelector('.pagination');
    paginationContainer.innerHTML = ''; 

    paginationData.links.forEach(link => {
        if (link.active) {
            paginationContainer.innerHTML += `
                <li class="page-item active">
                    <a class="page-link" href="javascript:void(0);">${link.label}</a>
                </li>`;
        } else if (link.url) {
            paginationContainer.innerHTML += `
                <li class="page-item">
                    <a class="page-link" href="javascript:void(0);" onclick="return loadPage(event, '${link.url}')">${link.label}</a>
                </li>`;
        } else {
            paginationContainer.innerHTML += `
                <li class="page-item disabled">
                    <span class="page-link">${link.label}</span>
                </li>`;
        }
    });
}

function displaySearchError(message) {
    const errorContainer = document.getElementById('search-error');
    errorContainer.textContent = message;
}

function clearError() {
    const errorContainer = document.getElementById('search-error');
    errorContainer.textContent = '';
}

function handleError(error) {
    if (error.response) {
        if (error.response.status === 404) {
            displaySearchError(error.response.data.message || "Food not found.");
        } else if (error.response.status === 500) {
            displaySearchError("An internal server error occurred. Please try again later.");
        } else {
            displaySearchError("An unexpected error occurred. Please try again.");
        }
    } else {
        displaySearchError("Failed to connect to the server. Please check your internet connection.");
    }
}

function loadPage(event, url) {
    event.preventDefault();
    const dateFilter = document.querySelector('.dropdown-menu .active')?.getAttribute('data-date') || null;
    const searchQuery = document.querySelector('input[name="search-field"]').value || null;
    const page = new URL(url).searchParams.get('page');
    FoodList(page, dateFilter, searchQuery);
}

const debouncedSearch = debounce(function() {
    const searchQuery = document.querySelector('input[name="search-field"]').value || null;
    FoodList(1, null, searchQuery); // Fetch new search results
}, 500); // Adjust debounce time as needed

document.getElementById('search-field').addEventListener('input', debouncedSearch);
</script>



