@extends('frontend.components.dashboard.dashboard-master')
@section('dashboard-content')
<div class="row mb-4">
    <div class="col-xl-12">
        <div class="card">
            <h5 class="card-header pb-3 border-bottom mb-3">Item Images</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-md mb-md-0 mb-2">
                        <div class="form-check custom-option custom-option-image custom-option-image-radio">
                            <label class="form-check-label custom-option-content" for="customRadioImg1">
                                <span class="custom-option-body">
                                    <img src="{{ asset('frontend/assets/img/backgrounds/3.jpg') }}" alt="radioImg" id="food-image" />
                                </span>
                            </label>
                            <input name="customRadioImage" class="form-check-input" type="radio" value="customRadioImg1" id="customRadioImg1" />
                        </div>
                    </div>
                    <div class="col-md mb-md-0 mb-2">
                        <div class="form-check custom-option custom-option-image custom-option-image-radio">
                            <label class="form-check-label custom-option-content" for="customRadioImg2">
                                <span class="custom-option-body">
                                    <img src="{{ asset('frontend/assets/img/backgrounds/8.jpg') }}" alt="radioImg" id="food-image2"/>
                                </span>
                            </label>
                            <input name="customRadioImage" class="form-check-input" type="radio" value="customRadioImg2" id="customRadioImg2" />
                        </div>
                    </div>
                    <div class="col-md">
                        <div class="form-check custom-option custom-option-image custom-option-image-radio">
                            <label class="form-check-label custom-option-content" for="customRadioImg3">
                                <span class="custom-option-body">
                                    <img src="{{ asset('frontend/assets/img/backgrounds/15.jpg') }}" alt="radioImg" id="food-image3"/>
                                </span>
                            </label>
                            <input name="customRadioImage" class="form-check-input" type="radio" value="customRadioImg3" id="customRadioImg3" />
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
                        <input type="text" class="d-none" id="complainID"/>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Item Name:</span>
                            <span id="food-name"></span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Item Gradients:</span>
                            <span id="food-gradients"></span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Item Description:</span>
                            <span id="food-description"></span>
                        </li>
                        <li class="mb-3">
                            <span class="fw-medium text-heading me-2">Complaint Status:</span>
                            <span class="rounded-pill" id="food-complain-status"></span>
                        </li>
                    </ul>
                    <div class="d-flex justify-content-center">
                        <a href="{{ route('complains') }}" class="btn btn-primary me-3">Back to complaint list</a>
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
                    <h5 class="card-header">Complain Conversation Details</h5>
                    <div class="table-responsive p-4 pt-0">
                      <table class="table table-hover">
                        <thead class="table-light">
                          <tr>
                            <th class="text-truncate">SL</th>
                            <th class="text-truncate">By</th>
                            <th class="text-truncate">Complain & Reply Message</th>
                            <th class="text-truncate">Date</th>
                            <th class="text-truncate">Time</th>
                          </tr>
                        </thead>
                        <tbody id="tableList">

                        </tbody>
                      </table>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn replyBtn btn-sm btn-outline-danger">Reply</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

<script>
    document.addEventListener("DOMContentLoaded", function () {
        ComplainDetailsInfo();

        $(document).on('click', '.replyBtn', function () {
            let id = document.getElementById('complainID').value; 
            $("#complainID").val(id);
            $("#reply-modal").modal('show'); 
        });
    });

    async function ComplainDetailsInfo() {
        showLoader();
        try {
            let url = window.location.pathname;
            let segments = url.split('/');
            let id = segments[segments.length - 1];

            document.getElementById('complainID').value = id;

            let res = await axios.get("/user/complain-details-info/" + id);
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

            let status = data.status;
            let badgeClass = 
                status === 'pending' ? 'bg-danger' : 
                status === 'under-review' ? 'bg-info' : 
                status === 'solved' ? 'bg-success' : 
                'badge-primary';

            let badgeHtml = `<span class="badge ${badgeClass}">${status}</span>`;
            document.getElementById('food-complain-status').innerHTML = badgeHtml;

            const replyBtn = document.querySelector('.replyBtn');
            if (status === 'under-review') {
                replyBtn.disabled = false;
            } else {
                 replyBtn.disabled = true;
            }

            let tableList = $("#tableList");
            tableList.empty(); 

            const mainComplain = data.message;
            const cmpDate = data.cmp_date;
            const cmpTime = data.cmp_time;

            let mainRow = `<tr>
                  <td><strong>1</strong></td>
                  <td><strong>User</strong></td>
                  <td><strong>${mainComplain}</strong></td>
                  <td><strong>${cmpDate}</strong></td>
                  <td><strong>${cmpTime}</strong></td>
                 </tr>`;
            tableList.append(mainRow);

            // Check if there are conversations
            if (data.conversations && data.conversations.length > 0) {
                data.conversations.forEach((conversation, index) => {
                    let sender;
                    
                    // Check sender_role and set sender name accordingly
                    if (conversation.sender_role === 'client') {
                        sender = 'Client';
                    } else if (conversation.sender_role === 'user') {
                        sender = 'User';
                    } else if (conversation.sender_role === 'admin') {
                        sender = 'Admin';
                    } else {
                        sender = 'Unknown'; // Fallback in case of any unexpected role
                    }

                    let row = `<tr>
                      <td>${index + 1}</td>
                      <td>${sender}</td>
                      <td>${conversation.reply_message}</td>
                      <td>${conversation.created_at.split('T')[0]}</td>
                      <td>${conversation.created_at.split('T')[1].split('.')[0]}</td>
                     </tr>`;
                    $('#tableList').append(row);
                });
            }  else {
                let row = `<tr>
                      <td colspan="4" class="text-center">No complain conversations available.</td>
                     </tr>`;
                tableList.append(row);
            }

        } catch (error) {
            if (error.response) {
                if (error.response.status === 404) {
                    errorToast(error.response.data.message || "Data not found.");
                } else if (error.response.status === 500) {
                    errorToast(error.response.data.error || "An internal server error occurred."); 
                } else {
                    errorToast("Request failed!");
                }
            } else {
                errorToast("Request failed! Please check your internet connection or try again later.");
            }
        }finally{
            hideLoader();
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