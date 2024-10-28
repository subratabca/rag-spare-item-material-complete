<div class="row">
  <div class="col-xl-6 col-lg-6 col-md-6 order-1 order-md-0">
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class="d-flex align-items-center flex-column">
            <img
              class="img-fluid rounded mb-3 mt-4"
              src="/upload/no_image.jpg"
              id="customer-image"
              height="120"
              width="120"
              alt="User avatar" />
            <div class="user-info text-center">
              <h4><span id="customer-name"> </span></h4>
              <span class="badge bg-label-danger rounded-pill">Customer</span>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between flex-wrap my-2 py-3">
          <div class="d-flex align-items-center me-4 mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-check mdi-24px"></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0" id="orders-count"> </h4>
              <span>Orders</span>
            </div>
          </div>
          <div class="d-flex align-items-center mt-3 gap-3">
            <div class="avatar">
              <div class="avatar-initial bg-label-primary rounded">
                <i class="mdi mdi-star-outline mdi-24px"></i>
              </div>
            </div>
            <div>
              <h4 class="mb-0" id="complains-count">568</h4>
              <span>Complains</span>
            </div>
          </div>
        </div>
        <h5 class="pb-3 border-bottom mb-3">Details</h5>
        <div class="info-container">
          <ul class="list-unstyled mb-4">
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Name:</span>
              <span id="customer-name2"></span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Email:</span>
              <span id="customer-email"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Contact:</span>
              <span id="customer-phone"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Registration Date:</span>
              <span id="customer-registration-date"> </span>
            </li>
            <li class="mb-3">
              <span class="fw-medium text-heading me-2">Registration Time:</span>
              <span id="customer-registration-time"> </span>
            </li>
          </ul>
          <div class="d-flex justify-content-center">
            <a href="/client/customer-list" class="btn btn-outline-primary">Back to customer list</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


<script>
  document.addEventListener("DOMContentLoaded", function () {
    CustomerDetailsInfo();
  });

  async function CustomerDetailsInfo() {
    showLoader();
    try {
      let url = window.location.pathname;
      let segments = url.split('/');
      let id = segments[segments.length - 1];

      let res = await axios.get("/client/customer/details/info/" + id);

      let imageUrl = res.data.data['image']
        ? `/upload/user-profile/small/${res.data.data['image']}`
        : `/upload/no_image.jpg`;

      let firstName = res.data.data['firstName'];
      let lastName = res.data.data['lastName'];
      let fullName = lastName ? `${firstName} ${lastName}` : firstName;

      let mobileNumber = res.data.data['mobile'];
      let phoneBadge = mobileNumber
        ? `<span class="badge bg-success">${mobileNumber}</span>`
        : `<span class="badge bg-info">Contact Number Not Found</span>`;

        let createdAt = new Date(res.data.data['created_at']);

        let registrationDate = createdAt.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });

        let registrationTime = createdAt.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });

      document.getElementById('customer-image').src = imageUrl;
      document.getElementById('customer-name').innerText = fullName;
      document.getElementById('orders-count').innerText = res.data.data['orders_count'];
      document.getElementById('complains-count').innerText = res.data.data['complains_count'];
      document.getElementById('customer-name2').innerText = fullName;
      document.getElementById('customer-email').innerText = res.data.data['email'];
      document.getElementById('customer-phone').innerHTML = phoneBadge;
      document.getElementById('customer-registration-date').innerText = registrationDate;
      document.getElementById('customer-registration-time').innerText = registrationTime;

    } catch (error) {
        if (error.response) {
            const { status, data } = error.response;
            const message = data.message || 'An unexpected error occurred';
            if (status === 500) {
                errorToast('Server error: ' + message);
            } else {
                errorToast(message); 
            }
        } else {
            errorToast('Error: ' + error.message); 
        }
    } finally {
      hideLoader();      
    }
  }

</script>

