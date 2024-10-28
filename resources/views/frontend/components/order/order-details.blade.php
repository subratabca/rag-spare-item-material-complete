@extends('frontend.components.dashboard.dashboard-master')
@section('dashboard-content')
<div class="row mb-4">
    <div class="col-xl-12">
      <div class="card">
        <h5 class="card-header  pb-3 border-bottom mb-3">Item Images</h5>
        <div class="card-body">
          <div class="row">
            <div class="col-md mb-md-0 mb-2">
              <div class="form-check custom-option custom-option-image custom-option-image-radio">
                <label class="form-check-label custom-option-content" for="customRadioImg1">
                  <span class="custom-option-body">
                    <img src="{{ asset('frontend/assets/img/backgrounds/3.jpg') }}" alt="radioImg" id="food-image" />
                  </span>
                </label>
              </div>
            </div>
            <div class="col-md mb-md-0 mb-2">
              <div class="form-check custom-option custom-option-image custom-option-image-radio">
                <label class="form-check-label custom-option-content" for="customRadioImg2">
                  <span class="custom-option-body">
                    <img src="{{ asset('frontend/assets/img/backgrounds/8.jpg') }}" alt="radioImg" id="food-image2"/>
                  </span>
                </label>
                <input
                  name="customRadioImage"
                  class="form-check-input"
                  type="radio"
                  value="customRadioImg2"
                  id="customRadioImg2" />
              </div>
            </div>
            <div class="col-md">
              <div class="form-check custom-option custom-option-image custom-option-image-radio">
                <label class="form-check-label custom-option-content" for="customRadioImg3">
                  <span class="custom-option-body">
                    <img src="{{ asset('frontend/assets/img/backgrounds/15.jpg') }}" alt="radioImg') }}" id="food-image3"/>
                  </span>
                </label>
                <input
                  name="customRadioImage"
                  class="form-check-input"
                  type="radio"
                  value="customRadioImg3"
                  id="customRadioImg3" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
      <div class="card mb-4">
          <h5 class="card-header pb-3 border-bottom mb-3">Item Details</h5>
                  <div class="card-body">
          <div class="info-container">
            <ul class="list-unstyled mb-4">
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Item Name:</span>
                <span id="food-name"></span>
              </li>
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Item Gradients :</span>
                <span id="food-gradients"></span>
              </li>
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Item Description :</span>
                <span id="food-description"></span>
              </li>
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Item Provider :</span>
                <span id="food-provider"></span>
              </li>
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Item Requested By:</span>
                <span id="food-requester"></span>
              </li>
              <li class="mb-3">
                <span class="fw-medium text-heading me-2">Delivery Status :</span>
                <span class="badge bg-label-success rounded-pill" id="food-delivery-status"></span>
              </li>
            </ul>
            <div class="d-flex justify-content-center">
              <a href="{{ route('orders') }}" class="btn btn-primary me-3">Back to order list</a>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card mb-4 border-2 border-primary">
            <div class="card-body">
                <div class="card mb-4">
                    <h5 class="card-header">Order Schedule Details</h5>
                    <div class="table-responsive">
                      <table class="table">
                        <thead class="table-light">
                          <tr>
                            <th class="text-truncate">Order Date</th>
                            <th class="text-truncate">Order Time</th>
                            <th class="text-truncate">Approve Order Date</th>
                            <th class="text-truncate">Approve Order Time</th>
                            <th class="text-truncate">Delivery Date</th>
                            <th class="text-truncate">Delivery Time</th>
                          </tr>
                        </thead>
                        <tbody id="tableList">

                        </tbody>
                      </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        OrderDetailsInfo();
    });

    async function OrderDetailsInfo() {
        let url = window.location.pathname;
        let segments = url.split('/');
        let id = segments[segments.length - 1];

        try {
            let res = await axios.get("/user/order-details-info/" + id);
            let data = res.data.data;

            document.getElementById('food-image').src = '/upload/food/large/' + data.food.image;

            if (data.food.food_images && data.food.food_images.length > 0) {
                document.getElementById('food-image2').src = data.food.food_images[0] 
                    ? '/upload/food/multiple/' + data.food.food_images[0].image 
                    : '/upload/no_image.jpg';
                
                document.getElementById('food-image3').src = data.food.food_images[1] 
                    ? '/upload/food/multiple/' + data.food.food_images[1].image 
                    : '/upload/no_image.jpg';
            } else {
                document.getElementById('food-image2').src = '/upload/no_image.jpg';
                document.getElementById('food-image3').src = '/upload/no_image.jpg';
            }

            document.getElementById('food-name').innerText = data.food.name;
            document.getElementById('food-gradients').innerText = data.food.gradients;
            document.getElementById('food-description').innerHTML = data.food.description;
            document.getElementById('food-provider').innerText = data.food.user.firstName;
            document.getElementById('food-requester').innerText = data.user.firstName;
            document.getElementById('food-delivery-status').innerText = data.status.charAt(0).toUpperCase() + data.status.slice(1);

            let tableList = $("#tableList");
            tableList.empty(); 

            let row = `<tr>
                      <td>${data.order_date}</td>
                      <td>${data.order_time}</td>
                      <td>${data.approve_date}</td>
                      <td>${data.approve_time}</td>
                      <td>${data.delivery_date}</td>
                      <td>${data.delivery_time}</td>
                     </tr>`;
            tableList.append(row);

        } catch (error) {
            console.error('Error fetching order details:', error);
            alert('Failed to fetch order details. Please try again later.');
        }
    }
</script>

<style type="text/css">
    .card-header {
        color: orange;
    }

    .custom-option-body img {
        width: 100%;  
        max-height: 200px; 
        object-fit: cover; 
    }
</style>

