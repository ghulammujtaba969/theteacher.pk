<?php
define('TITLE', 'Literal-&-IIdiomatic-Urdu-&-English');
//--------------------------------------------------
include "dbsetting/lms_vars_config.php";
include "dbsetting/classdbconection.php";
$dblms = new dblms();
include("include/header.php");
//--------------------------------------------------

echo '
<style>
   ul li:hover {
      color: #e5ae49;
   }
   .list-main-wrapper-one {
    background-color: white;
}
   .imges{
      content: "";
      position: relative;
      left: 115px;
      top: 35px;
      background-size: cover;
      background-repeat: no-repeat;
      width: 110px;
      height: 25px;
      transform: rotate(180deg);
   }
   
   .imges-2{
      content: "";
      position: relative;
      left: 1050px;
      top: -53px;
      background-size: cover;
      background-repeat: no-repeat;
      width: 110px;
      height: 25px;
   }
   
   p.haeding{
      font-size: 18px !important;
   }

   p{
      font-size: 16px !important;
   }

   .tab-three-wrapper .nav-pills .nav-item .nav-link.active {
      background: #ffffff;
      box-shadow: 0 0 15px 0 #17171714;
      border: 1px solid #fdf7ed;
      border-radius: 20px;
      color: #e5ae49 !important;
   }
</style>

<!-- inner- slider -->
   <div class="ibadat-inner-banner-main-wrapper">
      <div class="container custom-container">
         <div class="inner-page-title">
            <h2>Literal-&-Idiomatic-Urdu-&-English Syllabus</h2>
            <ul>
               <li><a href="Home.php">Home</a></li>
               <li><a href="javascript:;">| &nbsp; Literal-&-Idiomatic-Urdu-&-English</a></li>
            </ul>
         </div>
      </div>
   </div>
   <!--  -->
   <!-- Tab Buttons  -->

   <div class="tab-three-wrapper w-100 pt-5" style="background-color: #f8f2e8;">
      <div class="container">
         <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-12">
               <img class="imges" src="assets/images/home/line.png" alt="">
               <ul class="nav nav-pills mb-3 justify-content-center" id="pills-tab-three" role="tablist">
                  <li class="nav-item" role="presentation">
                     <a href="literal-n-iIdiomatic-urdu-n-english.php" ';
if (!LMS_VIEW) {
   echo 'class="nav-link active"';
} else {
   echo 'class="nav-link"';
}
echo ' >Detail
                     </a>
                  </li>
                  <li class="nav-item" role="presentation">
                     <a href="literal-n-iIdiomatic-urdu-n-english.php?view=lessons" ';
if (LMS_VIEW == 'lessons') {
   echo 'class="nav-link active"';
} else {
   echo 'class="nav-link"';
}
echo ' >Lessons
                     </a>
                  </li>
                  <li class="nav-item" role="presentation">
                     <a href="literal-n-iIdiomatic-urdu-n-english.php?view=register" ';
if (LMS_VIEW == 'register') {
   echo 'class="nav-link active"';
} else {
   echo 'class="nav-link"';
}
echo ' >How To Register
                     </a>
                  </li>
                  <li class="nav-item" role="presentation">
                     <a href="literal-n-iIdiomatic-urdu-n-english.php?view=videos" ';
if (LMS_VIEW == 'videos') {
   echo 'class="nav-link active"';
} else {
   echo 'class="nav-link"';
}
echo ' >Videos
                     </a>
                  </li>
               </ul>
               
               <img class="imges-2" src="assets/images/home/line.png" alt="">
            </div>
         </div>
      </div>
   </div>
   <!-- Tab Buttons  -->
';

//-------------------------------------------------------
if (!LMS_VIEW || LMS_VIEW == "detail") {
   include("include/Pages/literal-n-iIdiomatic-urdu-n-english/detail-literal-n-iIdiomatic-urdu-n-english.php");
}
//-------------------------------------------------------
if (LMS_VIEW == "lessons") {
   include("include/Pages/literal-n-iIdiomatic-urdu-n-english/lessons-literal-n-iIdiomatic-urdu-n-english.php");
}
//-------------------------------------------------------
if (LMS_VIEW == "register") {
   include("include/Pages/literal-n-iIdiomatic-urdu-n-english/register-literal-n-iIdiomatic-urdu-n-english.php");
}
//-------------------------------------------------------
if (LMS_VIEW == "videos") {
   include("include/Pages/literal-n-iIdiomatic-urdu-n-english/videos-literal-n-iIdiomatic-urdu-n-english.php");
}
//-------------------------------------------------------

echo '


               <div class="event-ticket-wrapper">
                  <p>Course Price</p>
                  <p>Rs1000/-</p>
               </div>
               <div class="event-blog-wrapper">
                  <div class="event-img">
                     <img src="assets/events/new_event.jpeg" alt="img">
                  </div>
                  <div class="event-text">
                     <h4><a href="javascript:;">LeadXcellence: 4-Day Principal Empowerment Series.</a></h4>
                     <ul>
                        <li>
                           <span>
                              <svg width="17" height="20" viewBox="0 0 17 20" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.24603 1.79122C2.99234 1.98649 2.74955 2.20328 2.51754 2.44096C0.839085 4.07384 0 6.0416 0 8.34557C0 10.6392 0.839249 12.5965 2.51754 14.2188C2.54449 14.2557 2.57129 14.306 2.59857 14.3693C2.6634 14.4062 2.72277 14.459 2.77669 14.5277L7.585 19.4808C8.20567 20.1731 8.81832 20.1731 9.42264 19.4808L14.4014 14.3294C14.4284 14.2925 14.4552 14.2556 14.4825 14.2186C16.1608 12.5961 17 10.6389 17 8.3454C17 6.04175 16.1608 4.07399 14.4825 2.44079C14.2503 2.20326 14.0022 1.9863 13.7377 1.79105C12.2534 0.596815 10.5024 0 8.48372 0C6.48674 0.000157086 4.74087 0.596976 3.24572 1.79137L3.24603 1.79122ZM1.12499 8.3216C1.12499 6.34045 1.84819 4.64162 3.29447 3.22575C4.7409 1.80984 6.48147 1.10195 8.51604 1.10195C10.54 1.10195 12.2751 1.80984 13.7216 3.22575C15.1679 4.64166 15.8911 6.34049 15.8911 8.3216C15.8911 10.3032 15.1679 12.0021 13.7216 13.4179C12.2753 14.8336 10.54 15.5421 8.51604 15.5421C6.48135 15.5421 4.74107 14.8338 3.29447 13.4179C1.84819 12.002 1.12499 10.3033 1.12499 8.3216ZM7.57688 8.08407C7.42573 8.09978 7.20974 8.1367 6.92923 8.19483C4.94876 8.68089 3.62655 9.9675 2.96255 12.0547C2.93559 12.1497 2.90317 12.2288 2.86546 12.2922L14.1343 12.2924C14.0533 12.1447 13.9753 11.9598 13.8997 11.7377C13.5326 10.7338 12.9849 9.93074 12.256 9.32862C11.5328 8.68391 10.6154 8.29067 9.50357 8.14758C9.45511 8.13187 9.38226 8.11066 9.28534 8.08427C9.81423 7.67724 10.1676 7.24426 10.3455 6.78463C10.5075 6.30927 10.4858 5.82839 10.2809 5.34234C10.092 4.9829 9.83304 4.69995 9.50358 4.49417C9.16917 4.28271 8.83157 4.20856 8.49184 4.2725C9.02074 4.69997 9.35274 5.08846 9.48752 5.43738C9.62777 5.78079 9.58476 6.15879 9.35803 6.57085C9.13145 6.99879 8.82626 7.26804 8.44319 7.37896C8.05471 7.45327 7.4743 7.41069 6.70263 7.25219L7.57688 8.08407ZM7.74714 4.60469L7.44739 5.45286H6.51637L7.26927 6.01541L6.99407 6.90299L7.7468 6.38001L8.49153 6.90299L8.22451 6.01541L8.97725 5.45286H8.05424L7.74714 4.60469Z"
                                    fill="#009146"></path>
                              </svg>
                           </span>
                           Mall Road, Murree, Pakistan
                        </li>
                        <li>
                           <span>
                              <svg width="17" height="17" viewBox="0 0 17 17" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path
                                    d="M8.5 0C3.81265 0 0 3.81265 0 8.5C0 13.1874 3.81265 17 8.5 17C13.1874 17 17 13.1874 17 8.5C17 3.81265 13.1874 0 8.5 0ZM8.5 1.18612C12.5463 1.18612 15.814 4.45361 15.814 8.50007C15.814 12.5464 12.5465 15.814 8.5 15.814C4.45353 15.814 1.18605 12.5465 1.18605 8.50007C1.18605 4.45375 4.45353 1.18612 8.5 1.18612ZM8.5 2.17449C8.17252 2.17449 7.90694 2.44006 7.90694 2.76755V8.50011C7.90694 8.66392 7.97258 8.81283 8.07985 8.92009L11.8544 12.6946C12.086 12.9261 12.4629 12.9263 12.6945 12.6946C12.9262 12.4631 12.9262 12.0861 12.6945 11.8545L9.09332 8.25326V2.7678C9.09332 2.44032 8.82789 2.17474 8.50026 2.17474L8.5 2.17449Z"
                                    fill="#009146"></path>
                              </svg>
                           </span>
                           08:30 am
                        </li>
                     </ul>
                  </div>
                  <div class="date-tag">
                     <h2>6</h2>
                     <span>July</span>
                  </div>
               </div>

               <!--div class="event-popular">
                  <h4>Our Popular Tags</h4>
                  <div class="cat action">
                     <label>
                        <input type="checkbox" value="1"><span>Community</span>
                     </label>
                     <label>
                        <input type="checkbox" value="1"><span>Islam</span>
                     </label>
                     <label>
                        <input type="checkbox" value="1"><span>Hadith</span>
                     </label>
                     <label>
                        <input type="checkbox" value="1"><span>Religion</span>
                     </label>
                     <label>
                        <input type="checkbox" value="1"><span>Event</span>
                     </label>
                  </div>
               </div-->
            </div>
         </div>
      </div>
   </div>
   <!--  -->

   <!-- subscribe -->
   <div class="ibadat-subcribe-main-wrapper mt-0">
      <div class="container custom-container">
         <div class="subcribe-wrapper">
            <div class="subcribe-text">
               <span>
                  <svg width="39" height="32" viewBox="0 0 39 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path d="M3.89971 6.8252L0 10.1207L3.89971 11.9733V6.8252Z" fill="white" />
                     <path
                        d="M33.1493 12.909V0.975145C33.1519 0.715618 33.05 0.46622 32.8666 0.28275C32.6834 0.0992884 32.4337 -0.00256658 32.1745 4.91648e-05H6.82436C6.56516 -0.0025624 6.31543 0.0992884 6.1323 0.28275C5.94884 0.466211 5.84698 0.715618 5.8496 0.975145V12.909L19.4991 19.4023L33.1493 12.909ZM12.6746 5.84987H26.3249C26.8632 5.84987 27.2997 6.28666 27.2997 6.82497C27.2997 7.36327 26.8632 7.80006 26.3249 7.80006H12.6746C12.1363 7.80006 11.6998 7.36327 11.6998 6.82497C11.6998 6.28666 12.1363 5.84987 12.6746 5.84987ZM11.6998 10.725C11.6972 10.4655 11.7991 10.2161 11.9825 10.0326C12.1657 9.84916 12.4154 9.7473 12.6746 9.74992H26.3249C26.8632 9.74992 27.2997 10.1864 27.2997 10.725C27.2997 11.2633 26.8632 11.6998 26.3249 11.6998H12.6746C12.4154 11.7024 12.166 11.6005 11.9825 11.4171C11.7991 11.2336 11.6972 10.9842 11.6998 10.725Z"
                        fill="white" />
                     <path
                        d="M19.4997 21.4498C19.3577 21.4475 19.218 21.4142 19.0904 21.3522L0.000476258 12.2656V30.2248C-0.00213531 30.484 0.0997155 30.7338 0.283177 30.9172C0.466638 31.1004 0.716045 31.2023 0.975238 31.1999H38.0252C38.2844 31.2023 38.5338 31.1004 38.7173 30.9172C38.9007 30.7338 39.0026 30.484 39 30.2248V12.2656L19.91 21.3522C19.7824 21.4142 19.6427 21.4475 19.5007 21.4498H19.4997Z"
                        fill="white" />
                     <path d="M35.0998 6.8252V11.9733L38.9995 10.1207L35.0998 6.8252Z" fill="white" />
                  </svg>
               </span>
               <h4>Subscribe Our Newsletter</h4>
            </div>
            <div class="subcribe-search">
               <input type="text" class="form-control" placeholder="Email Address">
               <button class="redButton"><span>Subscribe Now</span></button>
            </div>
         </div>
      </div>
   </div>
';
//-------------------------------------------------------
include("include/footer.php");
