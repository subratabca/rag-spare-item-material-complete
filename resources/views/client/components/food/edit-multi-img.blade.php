<div class="row">

  <div class="col-md-12">
    <div class="card mb-4">
        <div class="card-header header-elements">
            <span class="me-2"><h5>Update Multiple Item Image</h5></span>
            <div class="card-header-elements ms-auto">
                <a href="{{ route('client.foods') }}" type="button" class="btn btn-primary waves-effect waves-light">
                    <span class="tf-icon mdi mdi-arrow-left me-1"></span>Back To Item List
                </a>
            </div>
        </div>
      <div class="card-body demo-vertical-spacing demo-only-element">
        <form id="save-form">
            <div id="multi-images-container"></div>
        </form>
      </div>
    </div>
  </div>

</div>

<script>

document.addEventListener("DOMContentLoaded", function () {
    MultiImgInfo();
});

async function MultiImgInfo() {
    showLoader();
    try {
        let url = window.location.pathname;
        let segments = url.split('/');
        let id = segments[segments.length - 1];

        let res = await axios.get("/client/food/info/" + id);
          if (res.data && res.data.data.food_images) {
            renderMultiImages(res.data.data.food_images);
          }

    } catch (error) {
        if (error.response) {
            if (error.response.status === 404) {
                errorToast(error.response.data.message || "Data not found.");
            } 
            else if (error.response.status === 500) {
                errorToast(error.response.data.error || "An internal server error occurred."); 
            } 
            else {
                errorToast("Request failed!");
            }
        } else {
            errorToast("Request failed! Please check your internet connection or try again later.");
        }
    } finally{
      hideLoader();
    }
}

function updateMultiImgUrl(input, index) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById(`updateMultiImg${index}`).setAttribute('src', e.target.result);
    };
    reader.readAsDataURL(input.files[0]);
  }
}


function renderMultiImages(foodImages) {
  const container = document.getElementById('multi-images-container');
  container.innerHTML = ''; // Clear the container before rendering

  foodImages.forEach((image, index) => {
    const imgIndex = index + 1;
    const imgElement = `
      <div class="row mg-b-25">
        <div class="col-lg-12">
          <div class="row">
            <!-- Old image display -->
            <div class="col-lg-4">
              <div class="form-group">
                <label class="form-control-label">Old Related Image:</label><br>
                <img style="width: 150px; height: 100px;" id="oldMultiImg${imgIndex}" src="{{asset('/upload/food/multiple/${image.image}')}}"/>
              </div>
            </div>

            <!-- New image upload input -->
            <div class="col-lg-8">
              <div class="row">
                <div class="col-lg-6">
                  <label class="form-control-label">Upload New Related Food Image: <span class="tx-danger">*</span></label>
                  <input type="file" id="imgUpdate${imgIndex}" class="form-control" onChange="updateMultiImgUrl(this, ${imgIndex})">
                  <span class="error-message text-danger" id="update_image-error${imgIndex}"></span>
                </div>
                <div class="col-lg-6">
                  <img src="{{ asset('/upload/no_image.jpg') }}" id="updateMultiImg${imgIndex}" class="mt-1" width="150" height="100">
                </div>
              </div>
            </div>

            <!-- Update button -->
            <div class="col-lg-4 mt-4">


        <button type="button" onclick="updateMultiImg(${image.id}, ${imgIndex})" class="btn btn-primary btn-lg">
          <i class="mdi mdi-check me-2"></i>Update
        </button>
            </div>
          </div>
        </div>
      </div><hr>
    `;

    container.insertAdjacentHTML('beforeend', imgElement);
  });
}


  async function updateMultiImg(imageId, imgIndex) {
    let formData = new FormData();
    formData.append('id', imageId);
    const imgUpdateInput = document.getElementById(`imgUpdate${imgIndex}`);
    if (imgUpdateInput.files && imgUpdateInput.files[0]) {
      formData.append('image', imgUpdateInput.files[0]);
    } else {
      errorToast("Please select an image to update");
      return;
    }

    showLoader();
    const config = {
      headers: {
        'content-type': 'multipart/form-data'
      }
    };

    try {
      let res = await axios.post("/client/update-multi-image", formData, config);
      if (res.status === 200) {
        successToast(res.data.message || 'Update Success');
        window.location.href = '/client/food-list';
        document.getElementById('save-form').reset();
 
      } else {
        errorToast(res.data.message || "Request failed");
      }
    } catch (error) {
      if (error.response && error.response.status === 422) {
        let errorMessages = error.response.data.errors;
        for (let field in errorMessages) {
          if (errorMessages.hasOwnProperty(field)) {
            document.getElementById(`update_${field}-error${imgIndex}`).innerText = errorMessages[field][0];
          }
        }
      } else if (error.response && error.response.status === 500) {
        errorToast(error.response.data.error);
      } else {
        errorToast("Request failed!");
      }
    } finally{
      hideLoader();
    }
  }
</script>


