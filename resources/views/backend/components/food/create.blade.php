<div class="row">
  <div class="col-md-12">
    <div class="card mb-4">
      <h5 class="card-header">Create New Item</h5>
      <div class="card-body demo-vertical-spacing demo-only-element">
        <form id="save-form">
          <div class="row">
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
                <span class="error-message text-danger" id="expire-date-error"></span>
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
                <span class="error-message text-danger" id="collection-date-error"></span>
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
                <span class="error-message text-danger" id="start-collection-time-error"></span>
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
                <span class="error-message text-danger" id="end-collection-time-error"></span>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="mb-3">
                    <label for="formFile" class="form-label">Upload Item Image<span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="image" onChange="mainImageUrl(this)"/>
                    <img src="{{asset('/upload/no_image.jpg')}}" id="mainImage" class="mt-1" style="width: 150px; height: 100px;">
                  </div>
                  <span class="error-message text-danger" id="image-error"></span>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="mb-3">
                    <label for="formFileMultiple" class="form-label">Multiple Image(maximum 2 images)<span class="text-danger">*</span></label>
                    <input class="form-control" type="file" id="multi_image" multiple onChange="multiImageUrl(this)"/>
                  </div>
                  <span class="error-message text-danger" id="multi_image-error"></span>
                  <div id="multiImage" class="mt-1" style="display: flex; gap: 5px;">
                    <img src="{{asset('/upload/no_image.jpg')}}" id="defaultImage" style="width: 150px; height: 100px;">
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12 my-4">
              <div class="card">
                <h5 class="card-header">Food Description<span class="text-danger">*</span></h5>
                <div class="card-body">
                   @include('backend.components.editor')
                  <div id="snow-editor">

                  </div>
                  <span class="error-message text-danger" id="description-error"></span>
                </div>
              </div>
            </div>
            <div class="col-md-12 p-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="accept_tnc" />
                <label class="form-check-label" for="defaultCheck3"><a href="/admin/terms-conditions/food_upload" target="_blank">Accept T&C For Food Upload</a><span class="text-danger">*</span></label>
              </div>
              <span class="error-message text-danger" id="accept_tnc-error"></span>
            </div>
          </div>
        </form>
        <button onclick="Save()" class="btn btn-primary btn-lg">
          <i class="mdi mdi-check me-2"></i>Confirm
        </button>
      </div>
    </div>
  </div>

</div>


<script>
  var quill;
  document.addEventListener("DOMContentLoaded", function() {
      quill = new Quill('#snow-editor', {
          theme: 'snow',
          modules: {
              toolbar: '#toolbar'
          }
      });

      const form = document.getElementById('save-form');
      form.addEventListener('onclick', async function(event) {
          event.preventDefault();
          await Save();
      });
  });


  function mainImageUrl(input) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function (e) {
        $('#mainImage').attr('src', e.target.result).width(150).height(100);
      };
      reader.readAsDataURL(input.files[0]);
    }
  }


  function multiImageUrl(input) {
    $('#multiImage').empty();

    if (input.files) {
      Array.from(input.files).forEach(file => {
        let reader = new FileReader();
        reader.onload = function (e) {
          $('#multiImage').append(`
            <img src="${e.target.result}" class="mt-1" style="width: 150px; height: 100px; margin-right: 5px;">
          `);
        };
        reader.readAsDataURL(file);
      });
    }
  }



function resetCreateForm() {
  document.getElementById('save-form').reset();
  $('#mainImage').attr('src', '');
  $('#multiImage').empty();
  quill.setContents([]);
}



  async function Save() {
    let name = document.getElementById('name').value;
    let gradients = document.getElementById('TagifyBasic').value;
    let expire_date = document.getElementById('expire_date').value;
    let address = document.getElementById('address').value;
    let collection_date = document.getElementById('collection_date').value;
    let start_collection_time = document.getElementById('start_collection_time').value;
    let end_collection_time = document.getElementById('end_collection_time').value;
    let description = quill.root.innerHTML.trim();
    let image = document.getElementById('image').files[0];
    let multiImages = document.getElementById('multi_image').files;
    let accept_tnc = document.getElementById('accept_tnc').checked ? 1 : 0;


    document.getElementById('name-error').innerText = '';
    document.getElementById('gradients-error').innerText = '';
    document.getElementById('expire-date-error').innerText = '';
    document.getElementById('address-error').innerText = '';
    document.getElementById('collection-date-error').innerText = '';
    document.getElementById('start-collection-time-error').innerText = '';
    document.getElementById('end-collection-time-error').innerText = '';
    document.getElementById('description-error').innerHTML = '';
    document.getElementById('image-error').innerText = '';
    document.getElementById('multi_image-error').innerText = '';
    document.getElementById('accept_tnc-error').innerText = '';

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
      errorToast("Item colletion address required !");
    }
    else if (collection_date.length === 0) {
      errorToast("Item colletion date required !");
    }
    else if (start_collection_time.length === 0) {
      errorToast("Item colletion start time required !");
    }
    else if (end_collection_time.length === 0) {
      errorToast("Item colletion end time required !");
    }
    else if (quill.getText().trim().length === 0) {  
        errorToast("Item description required !");
    }
    else if (quill.getText().trim().length < 10) {
        errorToast("Item description must be at least 10 characters long!");
    }
    else if (!accept_tnc) {
        errorToast("You must accept the terms and conditions!");
    }
    else if (!image) {
      errorToast("Item image required !");
    } 
    else if (!multiImages) {
      errorToast("Multiple image required !");
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
      formData.append('accept_tnc', accept_tnc);
      formData.append('image', image);

      let multiImages = document.getElementById('multi_image').files;
      Array.from(multiImages).forEach((file, index) => {
          formData.append(`multi_images[${index}]`, file);
      });

      const config = {
        headers: {
          'content-type': 'multipart/form-data',
        },
      };

      try {
        let res = await axios.post("/admin/store/food", formData, config);
        if (res.status === 201) {
          successToast(res.data.message || 'Request success');
          window.location.href = '/admin/food-list';
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

