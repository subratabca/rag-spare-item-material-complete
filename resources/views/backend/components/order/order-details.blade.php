<div class="card mb-5">
    <div class="card-header header-elements">
        <span class="me-2"><h5>Order Details Information</h5></span>
        <div class="card-header-elements ms-auto">
            <a href="{{ route('admin.orders') }}" type="button" class="btn btn-primary waves-effect waves-light">
                <span class="tf-icon mdi mdi-arrow-left me-1"></span>Back To Order List
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <tbody>
                    <tr>
                        <th><strong>Item Image :</strong></th>
                        <td>
                            <img style="width: 150px; height: 100px;" id="food-image" src="{{asset('/upload/no_image.jpg')}}" alt="Food Image" />
                            <span id="food-images-container"></span>
                        </td>
                    </tr>
                    <tr>
                        <th><strong>Item Name :</strong></th>
                        <td id="food-name"></td>
                    </tr>
                    <tr>
                        <th><strong>Item Gradients :</strong></th>
                        <td id="food-gradients"></td>
                    </tr>
                    <tr>
                        <th><strong>Item Description :</strong></th>
                        <td id="food-description"></td>
                    </tr>
                    <tr>
                        <th><strong>Collection Address :</strong></th>
                        <td id="food-collection-address"></td>
                    </tr>
                    <tr>
                        <th><strong>Collection Date :</strong></th>
                        <td id="food-collection-date"></td>
                    </tr>
                    <tr>
                        <th><strong>Collection Time :</strong></th>
                        <td id="food-collection-time"></td>
                    </tr>
                    <tr>
                        <th><strong>Item Provider :</strong></th>
                        <td id="food-provider"></td>
                    </tr>
                    <tr>
                        <th><strong>Item Request By :</strong></th>
                        <td id="food-requester"></td>
                    </tr>
                    <tr>
                        <th><strong>Requester Email :</strong></th>
                        <td id="food-requester-email"></td>
                    </tr>
                    <tr>
                        <th><strong>Requester Phone :</strong></th>
                        <td id="food-requester-phone"></td>
                    </tr>
                    <tr>
                        <th>
                            <a href="/admin/terms-conditions/food_upload" target="_blank"><strong>Item Upload T&C :</strong></a>
                        </th>
                        <td id="food-upload-tnc"></td>
                    </tr>
                    <tr>
                        <th>
                            <a href="/admin/terms-conditions/request_approve" target="_blank"><strong>Order Request T&C :</strong></a>
                        </th>
                        <td id="food-order-request-tnc"></td>
                    </tr>
                    <tr>
                        <th>
                            <a href="/admin/terms-conditions/food_deliver" target="_blank"><strong>Item Delivery T&C :</strong></a>
                        </th>
                        <td id="food-delivery-tnc"></td>
                    </tr>
                    <tr>
                        <th><strong>Order Status :</strong></th>
                        <td id="food-delivery-status"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="card">
    <div class="card-header header-elements">
        <span class="me-2"><h5 class="text-danger">Order Schedule Details</h5></span>
        <div class="card-header-elements ms-auto">
            <a href="{{ route('admin.orders') }}" type="button" class="btn btn-primary waves-effect waves-light">
                <span class="tf-icon mdi mdi-arrow-left me-1"></span>Back To Order List
            </a>
        </div>
    </div>
    <div class="card-body" style="margin-top: 0px; padding-top: 0px;">
        <div class="table-responsive">
          <table class="table table-bordered">
              <thead>
                  <tr>
                      <th>Order Date</th>
                      <th>Order Time</th>
                      <th>Approve Date</th>
                      <th>Approve Time</th>
                      <th>Delivery Date</th>
                      <th>Delivery Time</th>
                  </tr>
              </thead>
              <tbody id="tableList1">
                  
              </tbody>
          </table>
        </div>
    </div>
</div>



<script>
  document.addEventListener("DOMContentLoaded", function () {
    OrderDetailsInfo();
  });

  async function OrderDetailsInfo() {
    showLoader();
    try {
      let url = window.location.pathname;
      let segments = url.split('/');
      let id = segments[segments.length - 1];

      let res = await axios.get("/admin/order/details/info/" + id);
      let foodImages = res.data.data.food.food_images;

      let parser = new DOMParser();
      let doc = parser.parseFromString(res.data.data['food']['description'], 'text/html');

      let collectionDate = new Date(res.data.data['food']['collection_date']);
      let formattedDate = collectionDate.toLocaleDateString('en-GB', {
          day: '2-digit',
          month: 'long',
          year: 'numeric'
      });

      let formattedStartTime = formatTime(res.data.data['food']['start_collection_time']);
      let formattedEndTime = formatTime(res.data.data['food']['end_collection_time']);

      document.getElementById('food-image').src = `/upload/food/small/${res.data.data['food']['image']}`;
      document.getElementById('food-name').innerText = res.data.data['food']['name'];
      document.getElementById('food-gradients').innerText = res.data.data['food']['gradients'];
      document.getElementById('food-description').innerText = doc.body.textContent;
      document.getElementById('food-collection-address').innerText = res.data.data['food']['address'];
      document.getElementById('food-collection-date').innerText = `${formattedDate}`;
      document.getElementById('food-collection-time').innerText = `${formattedStartTime} - ${formattedEndTime}`;
      document.getElementById('food-provider').innerText = res.data.data['food']['user']['firstName'];
      document.getElementById('food-requester').innerText = res.data.data['user']['firstName'];
      document.getElementById('food-requester-email').innerText = res.data.data['user']['email'];

      let mobileNumber = res.data.data['user']['mobile'];
      let phoneBadge = mobileNumber
      ? `<span class="badge bg-success">${mobileNumber}</span>`
      : `<span class="badge bg-info">Contact Number Not Found</span>`;

      document.getElementById('food-requester-phone').innerHTML = phoneBadge;

      let status = res.data.data['status'];
      let badgeClass = status === 'pending' ? 'bg-danger' : 'bg-success';
      document.getElementById('food-delivery-status').innerHTML = `<span class="badge ${badgeClass}">${status}</span>`;

      let foodImagesContainer = document.getElementById('food-images-container');
      foodImagesContainer.innerHTML = ''; 

      foodImages.forEach(image => {
          let imgElement = document.createElement('img');
          imgElement.src = `/upload/food/multiple/${image.image}`; 
          imgElement.style.width = '150px';
          imgElement.style.height = '100px';
          imgElement.style.marginRight = '10px'; 
          foodImagesContainer.appendChild(imgElement);
      });

      let food_upload_tnc_status = res.data.data.food['accept_tnc'];
      let tncText = food_upload_tnc_status === 0 ? 'Not Accepted' : 'Accepted';
      let tncClass = food_upload_tnc_status === 0 ? 'bg-danger' : 'bg-success';
      document.getElementById('food-upload-tnc').innerHTML = `<span class="badge ${tncClass}">${tncText}</span>`;

      let accept_order_request_tnc_status = res.data.data['accept_order_request_tnc'];
      let tncOrderReqText = accept_order_request_tnc_status === 0 ? 'Not Accepted' : 'Accepted';
      let tncOrderReqClass = accept_order_request_tnc_status === 0 ? 'bg-danger' : 'bg-success';
      document.getElementById('food-order-request-tnc').innerHTML = `<span class="badge ${tncOrderReqClass}">${tncOrderReqText}</span>`;

      let accept_food_deliver_tnc_status = res.data.data['accept_food_deliver_tnc'];
      let tncFoodDelText = accept_order_request_tnc_status === 0 ? 'Not Accepted' : 'Accepted';
      let tncFoodDelClass = accept_order_request_tnc_status === 0 ? 'bg-danger' : 'bg-success';
      document.getElementById('food-delivery-tnc').innerHTML = `<span class="badge ${tncFoodDelClass}">${tncFoodDelText}</span>`;

      let tableList1 = $("#tableList1");
      tableList1.empty();

      let orderDate = res.data.data.order_date === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.order_date}</span>`;

      let orderTime = res.data.data.order_time === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.order_time}</span>`;

      let approveDate = res.data.data.approve_date === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.approve_date}</span>`;

      let approveTime = res.data.data.approve_time === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.approve_time}</span>`;

      let deliveryDate = res.data.data.delivery_date === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.delivery_date}</span>`;

      let deliveryTime = res.data.data.delivery_time === null ? 
          '<span class="badge bg-danger">Pending</span>' : 
          `<span class="badge bg-info">${res.data.data.delivery_time}</span>`;

      let row1 = `<tr>
          <td>${orderDate}</td>
          <td>${orderTime}</td>
          <td>${approveDate}</td>
          <td>${approveTime}</td>
          <td>${deliveryDate}</td>
          <td>${deliveryTime}</td>
      </tr>`;

      tableList1.append(row1);


    } catch (error) {
      console.error('Error fetching food information:', error);
    }finally{
      hideLoader();      
    }
  }


  function formatTime(timeString) {
      let date = new Date('1970-01-01T' + timeString + 'Z');
      let hours = date.getUTCHours();
      let minutes = date.getUTCMinutes();
      let seconds = date.getUTCSeconds();

      let amPm = hours >= 12 ? 'PM' : 'AM';

      hours = hours % 12;
      hours = hours ? hours : 12;

      minutes = minutes < 10 ? '0' + minutes : minutes;
      seconds = seconds < 10 ? '0' + seconds : seconds;

      return `${hours}:${minutes}:${seconds} ${amPm}`;
  }

</script>


