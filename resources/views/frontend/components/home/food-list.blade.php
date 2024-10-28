<section id="landingFeatures" class="section-py landing-features">
  <div class="container">               
    <div class="app-academy"> 
        <div class="card mb-4">
          <div class="card-header d-flex flex-wrap justify-content-between gap-3">
            <div class="card-title mb-0 me-1">
              <h5 class="mb-1">Item List</h5>
              <p class="mb-0">Total 0 items found</p>
            </div>

            <div class="d-flex justify-content-md-end align-items-center gap-3 flex-wrap">
              <div class="d-flex align-items-center justify-content-between app-academy-md-80">
                <input type="search" id="search-field" name="search-field" value="" placeholder="Find your food" class="form-control me-2" />
                <div id="search-error" class="text-danger"></div>
              </div>
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

async function FoodList(page = 1, searchQuery = null) {
    showLoader();
    try {
        let url = `/food-list?page=${page}`;

        if (searchQuery) {
            url = `/search-food?query=${searchQuery}`; 
        }

        const res = await axios.get(url);
        const data = res.data;
        //console.log('------',data);
        const paginationContainer = document.querySelector('.pagination');

        if (data.status === 'success') {
            const foodData = searchQuery ? data.foods : data.foods.data;
            const totalItems = data.total;

            updateFoodList(foodData);
            clearError();
            updateTotalCount(totalItems);

            if (totalItems === 0) {
                paginationContainer.innerHTML = ''; 
            } else if (!searchQuery) {
                updatePagination(data.foods);
            }
        } else if (data.status === 'failed') {
            displaySearchError(data.message);
            paginationContainer.innerHTML = ''; 
        }

        // Scroll to the food list section after loading the content
        document.getElementById('gried-view').scrollIntoView({ behavior: 'smooth' });

    } catch (error) {
        handleError(error);
    } finally{
        hideLoader();
    }
}

function updateTotalCount(totalItems) {
    const totalItemsElement = document.querySelector('.card-title p.mb-0');
    
    if (totalItems > 0) {
        totalItemsElement.innerHTML = `Total ${totalItems} items found`;
        totalItemsElement.classList.remove('text-danger'); // Remove 'text-danger' if present
        totalItemsElement.classList.add('text-success'); // Add green color class
    } else {
        totalItemsElement.innerHTML = `No items found`;
        totalItemsElement.classList.remove('text-success'); // Remove 'text-success' if present
        totalItemsElement.classList.add('text-danger'); // Add red color class
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
    displaySearchError("An unexpected error occurred. Please try again.");
}

function loadPage(event, url) {
    event.preventDefault();
    const searchQuery = document.querySelector('input[name="search-field"]').value || null;
    const page = new URL(url).searchParams.get('page');
    FoodList(page, searchQuery);
}

const debouncedSearch = debounce(function() {
    const searchQuery = document.querySelector('input[name="search-field"]').value || null;
    FoodList(1, searchQuery); 
}, 500); 

document.getElementById('search-field').addEventListener('input', debouncedSearch);

</script>



