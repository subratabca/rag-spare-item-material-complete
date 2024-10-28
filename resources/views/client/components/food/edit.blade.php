<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
        <div class="card-header header-elements">
            <span class="me-2"><h5>Update Item Information</h5></span>
            <div class="card-header-elements ms-auto">
                <a href="{{ route('client.foods') }}" type="button" class="btn btn-primary waves-effect waves-light">
                    <span class="tf-icon mdi mdi-arrow-left me-1"></span>Back To Item List
                </a>
            </div>
        </div>
      <div class="card-body demo-vertical-spacing demo-only-element">
        <form id="save-form">
          <div class="row">
            <input type="text" class="d-none" id="updateID">
            <div class="col-md-6">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="text"
                class="form-control"
                id="name"
                placeholder="Enter food name" />
                <label for="exampleFormControlInput1">Item Name<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="name-error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="text"
                class="form-control"
                id="TagifyBasic"
                placeholder="Enter food gradients" />
                <label for="exampleFormControlInput1">Gradients<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="gradients-error"></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="date"
                class="form-control"
                id="expire_date"
                placeholder="Enter food expire date" />
                <label for="exampleFormControlInput1">Expire Date<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="expire_date-error"></span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="text"
                class="form-control"
                id="address"
                placeholder="Enter food collection address" />
                <label for="exampleFormControlInput1">Collection Address<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="address-error"></span>
              </div>
            </div>

            <div class="col-md-4">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="date"
                class="form-control"
                id="collection_date"
                placeholder="Enter food collection date" />
                <label for="exampleFormControlInput1">Collection Date<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="collection_date-error"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="time"
                class="form-control"
                id="start_collection_time"
                placeholder="Enter food collection start time" />
                <label for="exampleFormControlInput1">Collection Time(From)<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="start_collection_time-error"></span>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating form-floating-outline mb-4">
                <input
                type="time"
                class="form-control"
                id="end_collection_time"
                placeholder="Enter food collection end time" />
                <label for="exampleFormControlInput1">Collection Time(To)<span class="text-danger">*</span></label>
                <span class="error-message text-danger" id="end_collection_time-error"></span>
              </div>
            </div>

            <div class="col-md-12">
              <div class="card">
                <div class="card-body">

                  <div class="row">
                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="formFile" class="form-label">Old Item Image<span class="text-danger">*</span></label><br>
                        <img src="{{ asset('/upload/no_image.jpg')}}" id="oldImg" class="mt-1" style="width: 150px; height: 100px;">
                      </div>
                    </div>

                    <div class="col-lg-4">
                      <div class="mb-3">
                        <label for="formFile" class="form-label">Upload New Item Image<span class="text-danger">*</span></label>
                        <input class="form-control" type="file" id="imgUpdate" onChange="updateImgUrl(this)"/>
                        <img src="{{asset('/upload/no_image.jpg')}}" id="updateImg" class="mt-1" style="width: 150px; height: 100px;">
                      </div>
                      <span class="error-message text-danger" id="image-error"></span>
                    </div>
                  </div>

                </div>
              </div>
            </div>

            <div class="col-md-12 my-4">
              <div class="card">
                <h5 class="card-header">Item Description<span class="text-danger">*</span></h5>
                <div class="card-body">
                  @include('client.components.editor')
                  <div id="snow-editor">

                  </div>
                  <span class="error-message text-danger" id="description-error"></span>
                </div>
              </div>
            </div>
          </div>
        </form>
        <button onclick="updateFood()" class="btn btn-primary btn-lg">
          <i class="mdi mdi-check me-2"></i>Update
        </button>
      </div>
    </div>
  </div>

</div>


<script type="text/javascript">
  function updateImgUrl(input){
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e){
        $('#updateImg').attr('src',e.target.result).width(150).height(100);
      };
      reader.readAsDataURL(input.files[0]);
    }
  } 
</script>


<script>
var quill;
document.addEventListener("DOMContentLoaded", function () {
    FoodDetailsInfo();

    quill = new Quill('#snow-editor', {
        theme: 'snow',
        modules: {
            toolbar: '#toolbar'
        }
    });

    const form = document.getElementById('save-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();
        await updateFood();
    });
});

async function FoodDetailsInfo() {
    showLoader();
    try {
        let url = window.location.pathname;
        let segments = url.split('/');
        let id = segments[segments.length - 1];

        let res = await axios.get("/client/food/info/" + id);
        document.getElementById('updateID').value = id;
        document.getElementById('name').value = res.data.data['name'];

        let gradients = res.data.data['gradients'].split(','); 
        let tagify = new Tagify(document.getElementById('TagifyBasic'));
        tagify.addTags(gradients);

        document.getElementById('expire_date').value = res.data.data['expire_date'];
        document.getElementById('address').value = res.data.data['address'];
        document.getElementById('collection_date').value = res.data.data['collection_date'];
        document.getElementById('start_collection_time').value = res.data.data['start_collection_time'];
        document.getElementById('end_collection_time').value = res.data.data['end_collection_time'];

        const imageElement = document.getElementById('oldImg');
        if (res.data.data['image']) {
            imageElement.src = `/upload/food/medium/${res.data.data['image']}`; 
        } else {
            imageElement.src = '/upload/no_image.jpg'; 
        }

        quill.root.innerHTML = res.data.data['description'];

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

function resetCreateForm() {
    document.getElementById('save-form').reset();
    $('#mainImage').attr('src', '');
    $('#multiImage').empty();
    quill.setContents([]);
}

async function updateFood() {
    let name = document.getElementById('name').value;
    let gradients = document.getElementById('TagifyBasic').value;
    let expire_date = document.getElementById('expire_date').value;
    let address = document.getElementById('address').value;
    let collection_date = document.getElementById('collection_date').value;
    let start_collection_time = document.getElementById('start_collection_time').value;
    let end_collection_time = document.getElementById('end_collection_time').value;
    let description = quill.root.innerHTML.trim();
    let image = document.getElementById('imgUpdate').files[0];
    let updateID = document.getElementById('updateID').value;

    document.getElementById('name-error').innerText = '';
    document.getElementById('gradients-error').innerText = '';
    document.getElementById('expire_date-error').innerText = '';
    document.getElementById('address-error').innerText = '';
    document.getElementById('collection_date-error').innerText = '';
    document.getElementById('start_collection_time-error').innerText = '';
    document.getElementById('end_collection_time-error').innerText = '';
    document.getElementById('description-error').innerHTML = '';
    document.getElementById('image-error').innerText = '';

    if (name.length === 0) {
        errorToast("Item name required !");
    } 
    else if (gradients.length === 0) {
        errorToast("Item gradients required !");
    } 
    else if (expire_date.length === 0) {
        errorToast("Item expire date required !");
    }
    else if (address.length === 0) {
        errorToast("Item collection address required !");
    }
    else if (collection_date.length === 0) {
        errorToast("Item collection date required !");
    }
    else if (start_collection_time.length === 0) {
        errorToast("Item collection start time required !");
    }
    else if (end_collection_time.length === 0) {
        errorToast("Item collection end time required !");
    }
    else if (quill.getText().trim().length === 0) {  
        errorToast("Item description required!");
    }
    else if (quill.getText().trim().length < 10) {
        errorToast("Item description must be at least 10 characters long!");
    }
    else {
        let formData = new FormData();
        formData.append('name', name);
        formData.append('gradients', gradients);
        formData.append('expire_date', expire_date);
        formData.append('address', address);
        formData.append('collection_date', collection_date);
        formData.append('start_collection_time', start_collection_time);
        formData.append('end_collection_time', end_collection_time);
        formData.append('description', description);
        if (image) {
            formData.append('image', image);
        }
        formData.append('id', updateID);

        const config = {
            headers: {
                'content-type': 'multipart/form-data'
            }
        };

        try {
            let res = await axios.post("/client/update/food", formData, config);
            if (res.status === 200) {
                successToast(res.data.message || 'Update Success');
                window.location.href = '/client/food-list';
                resetCreateForm(); 
            } else {
                errorToast(res.data.message || "Request failed");
            }
        } catch (error) {
            if (error.response && error.response.status === 422) {
                let errorMessages = error.response.data.errors;
                for (let field in errorMessages) {
                    if (errorMessages.hasOwnProperty(field)) {
                        document.getElementById(`${field}-error`).innerText = errorMessages[field][0];
                    }
                }
            } else if (error.response && error.response.status === 400) {
                errorToast(error.response.data.message || "Invalid address or coordinates not found.");
            } else if (error.response && error.response.status === 500) {
                errorToast(error.response.data.error);
            } else {
                errorToast("Request failed!");
            }
        }
    }
}
</script>


