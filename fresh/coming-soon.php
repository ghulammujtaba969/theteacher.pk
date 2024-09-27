<?php
define('TITLE', 'Coming-Soon');
//--------------------------------------------------
include "dbsetting/lms_vars_config.php";
include "dbsetting/classdbconection.php";
$dblms = new dblms();
include("include/header.php");
//--------------------------------------------------

echo '
<!-- inner- slider -->
<div class="ibadat-inner-banner-main-wrapper">
      <div class="container custom-container">
         <div class="inner-page-title">
            <h2>Learn How to Pray Namaz (Prayer Timings)</h2>
            <ul>
               <li><a href="javascript:;">Home</a></li>
               <li><a href="javascript:;">| &nbsp; Learn How to Pray Namaz (Prayer Timings)</a></li>
            </ul>
         </div>
      </div>
   </div>
   <!--  -->
      <!-- Islamic Prayer -->
      <div class="ibadat-islamic-praying-main-wrapper learn-pray-wrappper">
         <div class="container custom-container">
            <div class="center-title">
               <h4>The-Teacher.pk</h4>
               <h2>Coming-Soon</h2>
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
include("include/footer.php");
