<section class="section-py bg-body first-section-pt">
  <div class="container"> 
    <form id="save-form">     
      <div class="card g-3 mt-5">
        <div class="card-body row g-3">
          <div class="col-lg-7">
            <div class="card academy-content shadow-none border">
              <div class="p-2">
                <div class="cursor-pointer">
                  <div class="row">
                    <!-- Gallery effect-->
                    <div class="col-12">
                      <div id="gallery-wrapper">
                        <!-- Main gallery -->
                        <div class="gallery-main">
                          <img id="mainImage" src="/upload/no_image.jpg" alt="Main Image" style="width: 100%; height: auto;" />
                        </div>
                        <!-- Thumbnail gallery -->
                        <div class="gallery-thumbs mt-3" id="galleryThumbImages" style="display: flex; gap: 10px;">
                          <!-- Thumbnails will be injected here -->
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-5">
            <div class="border rounded p-3 mb-3">
              <div class="bg-lighter rounded p-3">
                <h6>Food Name: <span id="food-name"></span></h6><hr class="my-4" />
                <h6>Gradients: <span id="gradients"></span></h6><hr class="my-4" />
                <h6>Expire Date: <span id="expire-date"></span></h6><hr class="my-4" />
                <h6>Collection Location: <span id="address"></span></h6><hr class="my-4" />
                <h6>Collection Date: <span id="collection-date"></span></h6><hr class="my-4" />
                <h6>Collection Time: <span id="end-collection-time"></span></h6><hr class="my-4" />
                <h6>Description:</h6> <span id="food-description"></span><hr class="my-4" />
                <h6>Provided By: <span id="client-name"></span></h6><hr class="my-4" />
              </div>
            </div>
            <div class="d-grid">
              <button onclick="Save()" class="btn btn-primary btn-next">Request Food</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>


<script>
  let url = window.location.pathname;
  let segments = url.split('/');
  let id = segments[segments.length - 1];

  async function FoodDetailsInfo() {
      try {
          let res = await axios.get("/food-details-info/" + id);
          let data = res.data['data'];

          let mainImageElement = document.getElementById('mainImage');
          let mainImage = '{{ asset("/upload/food/large/") }}/' + data.image;
          mainImageElement.src = mainImage;

          let galleryThumbs = document.getElementById('galleryThumbImages');
          let thumbHTML = '';

          thumbHTML += `<img src="${mainImage}" alt="Thumb Image" class="thumb-img" style="cursor: pointer; width: 100px; height: auto;" onclick="updateMainImage('${mainImage}')">`;

          if (data.food_images && data.food_images.length > 0) {
              data.food_images.forEach(foodImage => {
                  let thumbImage = '{{ asset("/upload/food/multiple/") }}/' + foodImage.image;
                  thumbHTML += `<img src="${thumbImage}" alt="Thumb Image" class="thumb-img" style="cursor: pointer; width: 100px; height: auto;" onclick="updateMainImage('${thumbImage}')">`;
              });
          }

          galleryThumbs.innerHTML = thumbHTML;

          // Update basic food details
          document.getElementById('food-name').innerText = data['name'];
          document.getElementById('gradients').innerText = data['gradients'];
          document.getElementById('food-description').innerHTML = data['description'];
          document.getElementById('expire-date').innerText = data['expire_date'];
          document.getElementById('address').innerText = data['address'];
          document.getElementById('collection-date').innerText = data['collection_date'];
          document.getElementById('end-collection-time').innerText = data['end_collection_time'];
          document.getElementById('client-name').innerText = data['user']['firstName'];

      } catch (error) {
          console.error(error);
      }
  }

  function updateMainImage(imageSrc) {
      let mainImageElement = document.getElementById('mainImage');
      mainImageElement.src = imageSrc;
  }

  async function Save() {
      try {
          let res = await axios.post("/user/store/food-request", { id: id });

          if (res.status === 201) {
              successToast(res.data.message || 'Request success');
              window.location.href = '/';
          } else {
              errorToast(res.data.message || "Request failed");
          }
      } catch (error) {
          if (error.response) {
              if (error.response.status === 401) {
                  window.location.href = '/user/login';
              } else if (error.response.status === 422) {
                  if (error.response.data.message) {
                      errorToast(error.response.data.message);
                  }
                  if (error.response.data.errors) {
                      let errorMessages = error.response.data.errors;
                      for (let field in errorMessages) {
                          if (errorMessages.hasOwnProperty(field)) {
                              errorMessages[field].forEach(msg => errorToast(msg));
                          }
                      }
                  }
              } else if (error.response.status === 404) {
                  errorToast(error.response.data.message || "Resource not found.");
              } else if (error.response.status === 500) {
                  errorToast(error.response.data.error || "An internal server error occurred.");
              } else if (error.response.status === 400) {
                  errorToast(error.response.data.message || "Bad request.");
                  window.location.href = '/';
              } else {
                  errorToast("Request failed!");
              }
          } else {
              errorToast("Request failed!");
          }
          console.error(error);
      }
  }
</script>

<style>
  .gallery-main {
    width: 80%;
    height: 400px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-color: #f0f0f0;
  }

  .gallery-main img {
    max-width: 100%;
    max-height: 100%;
    object-fit: cover;
  }

  .gallery-thumbs {
    display: flex;
    gap: 10px;
    justify-content: center;
    align-items: center;
  }

  .gallery-thumbs img {
    width: 100px; 
    min-height: 150px; 
    object-fit: cover;
    cursor: pointer;
    border: 1px solid #ddd;
  }
</style>
