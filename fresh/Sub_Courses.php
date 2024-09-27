<?php
define('TITLE', 'Sub_Courses');
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
            <h2>Contact</h2>
            <ul>
               <li><a href="javascript:;">Home</a></li>
               <li><a href="javascript:;">| &nbsp; Contact</a></li>
            </ul>
         </div>
      </div>
   </div>
   <!--  -->
   <div class="ibadat-contact-main-wrapper">
      <div class="container custom-container">
         <div class="contact-top-block">
            <div class="block  wow fadeInUp" data-wow-delay="100ms">
               <span>
                  <svg width="43" height="69" viewBox="0 0 43 69" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path
                        d="M19.8968 61.9935C18.5543 59.6707 16.7879 56.9317 14.8704 53.9614C8.30621 43.7837 0 30.9083 0 21.1346C0 15.2998 2.36688 10.0156 6.19123 6.19123C10.0151 2.36739 15.2998 0 21.1346 0C26.9694 0 32.2546 2.36688 36.078 6.19123C39.9018 10.0151 42.2692 15.2998 42.2692 21.1346C42.2692 30.9081 33.9616 43.7871 27.3949 53.9639C25.462 56.9608 23.6786 59.7242 22.3711 61.9896C21.9792 62.6725 21.1057 62.908 20.4222 62.516C20.1962 62.3865 20.0192 62.2046 19.8962 61.9946L19.8968 61.9935ZM28.3304 58.7646C27.5635 58.5826 27.0895 57.8121 27.2715 57.0451C27.4535 56.2782 28.224 55.8042 28.991 55.9862C31.1328 56.4977 32.9294 57.2206 34.1942 58.0816C35.6801 59.093 36.5246 60.3404 36.5246 61.7549C36.5246 63.8493 34.6047 65.6137 31.5008 66.7682C28.8169 67.7672 25.147 68.3862 21.1338 68.3862C17.12 68.3862 13.4491 67.7672 10.7667 66.7682C7.66337 65.6132 5.74294 63.8493 5.74294 61.7549C5.74294 60.3404 6.5879 59.093 8.07333 58.0816C9.33828 57.2196 11.1362 56.4976 13.2771 55.9862C14.0441 55.8042 14.8146 56.2782 14.9966 57.0451C15.1786 57.8121 14.7046 58.5826 13.9376 58.7646C12.1422 59.1936 10.6743 59.7731 9.68678 60.445C9.0018 60.9115 8.61083 61.3635 8.61083 61.7544C8.61083 62.5284 9.81629 63.3664 11.7637 64.0908C14.1301 64.9723 17.4419 65.5173 21.1338 65.5173C24.8257 65.5173 28.1376 64.9724 30.5039 64.0908C32.4508 63.3664 33.6568 62.5279 33.6568 61.7544C33.6568 61.3634 33.2658 60.9115 32.5809 60.445C31.5939 59.7736 30.1264 59.1936 28.3311 58.7646H28.3304ZM21.1337 11.724C23.7301 11.724 26.0855 12.779 27.7879 14.4814C29.4902 16.1839 30.5453 18.5392 30.5453 21.1356C30.5453 23.7285 29.4903 26.0804 27.7879 27.7848C26.0854 29.4923 23.7301 30.5472 21.1337 30.5472C18.5408 30.5472 16.1889 29.4922 14.4845 27.7898C12.7771 26.0804 11.7221 23.7289 11.7221 21.1356C11.7221 18.5392 12.7771 16.1838 14.4795 14.4814L14.5615 14.4059C16.259 12.7475 18.5793 11.724 21.1337 11.724ZM25.761 16.5084C24.5785 15.3259 22.9411 14.5924 21.1337 14.5924C19.3557 14.5924 17.7458 15.2984 16.5674 16.4428L16.5064 16.5078C15.3239 17.6903 14.5904 19.3277 14.5904 21.1351C14.5904 22.944 15.3229 24.5815 16.5038 25.7649C17.6873 26.9459 19.3247 27.6783 21.1336 27.6783C22.941 27.6783 24.5785 26.9453 25.7609 25.7623C26.9443 24.5814 27.6768 22.9439 27.6768 21.1351C27.6768 19.3277 26.9439 17.6902 25.7609 16.5078L25.761 16.5084ZM17.2784 52.416C18.6753 54.5788 19.9917 56.6208 21.1347 58.4878C22.2887 56.6014 23.5991 54.5714 24.9876 52.4175C31.3503 42.5554 39.4011 30.0773 39.4011 21.1343C39.4011 16.0896 37.3557 11.5222 34.0513 8.2184C30.7469 4.91461 26.1791 2.86861 21.1341 2.86861C16.0893 2.86861 11.5219 4.91397 8.21815 8.2184C4.91435 11.5228 2.86836 16.0901 2.86836 21.1343C2.86836 30.075 10.9176 42.5529 17.2793 52.4149L17.2784 52.416Z"
                        fill="#009146" />
                  </svg>
               </span>
               <h4>Location</h4>
               <p>Marollien Street, No. 14, 2nd floor</p>
            </div>
            <div class="block  wow fadeInUp" data-wow-delay="200ms">
               <span>
                  <svg width="56" height="59" viewBox="0 0 56 59" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path
                        d="M51.8428 22.216C52.8299 22.453 53.7142 22.9621 54.4094 23.6577C55.3903 24.6387 55.9999 25.9944 55.9999 27.4835V52.6552C55.9999 54.1453 55.3907 55.5 54.4094 56.481C53.4285 57.4619 52.0728 58.0714 50.5837 58.0714H6.31858C4.82854 58.0714 3.47378 57.4623 2.49282 56.481C1.51192 55.5001 0.902344 54.1453 0.902344 52.6552V27.4835C0.902344 25.9944 1.51151 24.6387 2.49282 23.6577C3.18845 22.9621 4.07274 22.453 5.05943 22.216V7.5505C5.05943 5.47499 5.90823 3.58745 7.27493 2.21988C8.64688 0.848832 10.5336 0 12.6099 0H44.2914C46.3669 0 48.2545 0.848798 49.622 2.2155C50.9931 3.58745 51.8419 5.47511 51.8419 7.5505L51.8428 22.216ZM3.61164 53.6852L19.9434 38.0804L5.85871 24.6215C5.2456 24.7203 4.69563 25.0139 4.27351 25.4361C3.74817 25.9614 3.42209 26.6869 3.42209 27.4826V52.6543C3.42209 53.0164 3.48968 53.364 3.61212 53.6844L3.61164 53.6852ZM21.7601 36.3445L27.5876 30.7756C28.0734 30.3134 28.8366 30.3178 29.3176 30.7756L35.1446 36.3437L49.3259 22.7927V7.54972C49.3259 6.16282 48.7602 4.90278 47.8517 3.9917C46.9406 3.08278 45.6801 2.51751 44.2937 2.51751H12.6122C11.2248 2.51751 9.96524 3.08323 9.05416 3.9917C8.14525 4.90282 7.57997 6.16327 7.57997 7.54972V22.7939L21.7613 36.3448L21.7601 36.3445ZM36.9616 38.0798L53.2933 53.6857C53.4157 53.3653 53.4833 53.0177 53.4833 52.6557V27.484C53.4833 26.6883 53.1573 25.9628 52.6319 25.4374C52.2097 25.0152 51.6594 24.7216 51.0463 24.6229L36.9627 38.0806L36.9616 38.0798ZM51.4687 55.4148L34.3044 39.0123L34.2807 38.99L34.2579 38.9676L28.4533 33.421L22.6495 38.9671L22.6482 38.9684L22.6254 38.9908L22.6017 39.0132L5.4374 55.4146C5.7161 55.5046 6.01409 55.5537 6.32175 55.5537H50.5868C50.8945 55.5537 51.1916 55.5046 51.4703 55.4146L51.4687 55.4148ZM39.0817 16.4719C39.0817 17.5801 38.6292 18.5869 37.8985 19.3181C37.1708 20.0501 36.1632 20.5013 35.0524 20.5013C34.0798 20.5013 33.184 20.1519 32.4848 19.5717C32.3497 19.7473 32.2048 19.9128 32.0495 20.0681L31.9731 20.1388C31.0567 21.0179 29.816 21.559 28.4537 21.559C27.0546 21.559 25.7853 20.9889 24.8624 20.0681L24.8558 20.0615C23.9359 19.1386 23.3671 17.8702 23.3671 16.4724C23.3671 15.0693 23.9372 13.7966 24.8571 12.8762L24.9335 12.8055C25.8481 11.9273 27.0893 11.3861 28.4538 11.3861C29.8547 11.3861 31.1247 11.9563 32.046 12.8762C32.9532 13.779 33.5198 15.0224 33.5396 16.3946C33.5396 16.8392 33.6638 17.2211 33.9841 17.5414C34.254 17.8157 34.6328 17.9834 35.0528 17.9834C35.4706 17.9834 35.8494 17.814 36.1224 17.5414C36.3967 17.2715 36.5643 16.8928 36.5643 16.4719C36.5643 14.2283 35.6576 12.1999 34.1939 10.7366C32.7263 9.26811 30.6973 8.3613 28.4537 8.3613C26.2133 8.3613 24.1843 9.26935 22.7175 10.7366C21.2512 12.2029 20.3431 14.2313 20.3431 16.4719C20.3431 18.7141 21.2503 20.7436 22.7153 22.2102C24.182 23.6748 26.2118 24.5824 28.4536 24.5824C29.4345 24.5824 30.3694 24.4116 31.2277 24.1C32.1217 23.7753 32.9464 23.2929 33.6662 22.6894C34.1981 22.2444 34.9911 22.3151 35.4362 22.847C35.8812 23.3789 35.8105 24.172 35.2786 24.617C34.3425 25.4017 33.2624 26.032 32.084 26.4598C30.9455 26.8728 29.7206 27.0997 28.4539 27.0997C25.5231 27.0997 22.8666 25.909 20.9429 23.9867C19.0158 22.0592 17.8255 19.4021 17.8255 16.4713C17.8255 13.5374 19.0153 10.8799 20.9385 8.95673C22.8616 7.03358 25.5199 5.84285 28.4539 5.84285C31.3825 5.84285 34.0387 7.03358 35.9641 8.95673L35.9707 8.96419C37.8929 10.8887 39.0823 13.544 39.0823 16.4712L39.0817 16.4719ZM30.272 14.6536C29.809 14.1906 29.1656 13.904 28.4537 13.904C27.7669 13.904 27.1446 14.1708 26.6841 14.604L26.6359 14.6554C26.172 15.1193 25.8845 15.7627 25.8845 16.4724C25.8845 17.186 26.1707 17.8276 26.6319 18.2893C27.098 18.7554 27.7401 19.0416 28.4533 19.0416C29.1406 19.0416 29.7633 18.7756 30.2198 18.3429L30.2703 18.2893C30.7342 17.8254 31.0225 17.1829 31.0225 16.4723C31.0225 15.7627 30.7351 15.1193 30.272 14.654L30.272 14.6536Z"
                        fill="#009146" />
                  </svg>
               </span>
               <h4>Email</h4>
               <p> <a href="mailto:mosqueexample@gmail.com">mosqueexample@gmail.com</a> </p>
            </div>
            <div class="block  wow fadeInUp" data-wow-delay="300ms">
               <span>
                  <svg width="63" height="63" viewBox="0 0 63 63" fill="none" xmlns="http://www.w3.org/2000/svg">
                     <path
                        d="M50.498 62.9678C49.6135 62.9678 48.7194 62.8624 47.8376 62.6502C22.8779 56.6361 6.9041 40.6731 0.359992 15.2045C-0.260012 12.791 -0.0746137 10.2856 0.881046 8.15C1.76921 6.16511 3.12065 4.36249 4.78881 2.93614L6.53923 1.43948C8.79872 -0.492151 12.0939 -0.478303 14.3723 1.47231C18.3933 4.91397 21.7203 8.6687 24.2614 12.6314C25.7524 14.9574 25.5073 17.9673 23.664 19.9507C22.885 20.7885 22.0167 21.6291 21.0823 22.4492C20.4744 22.9823 20.2903 23.852 20.6353 24.5644C24.4722 32.5052 30.4438 38.4764 38.3833 42.3136C39.0958 42.6576 39.9649 42.4741 40.4985 41.8661C41.3215 40.9285 42.1616 40.0602 42.997 39.2844C44.98 37.4411 47.9898 37.1956 50.3163 38.6871C54.279 41.2281 58.0333 44.5556 61.4754 48.5762C63.426 50.8555 63.4399 54.1497 61.5078 56.4092L60.0121 58.1596C58.57 59.8458 56.7456 61.2064 54.7372 62.0946C53.427 62.673 51.9766 62.9675 50.4981 62.9675L50.498 62.9678ZM10.4238 2.54752C9.62807 2.54752 8.83703 2.82215 8.19292 3.3728L6.4425 4.86946C5.05875 6.0526 3.93848 7.5464 3.20287 9.18959C2.48115 10.8018 2.34707 12.7132 2.82468 14.5718C9.11621 39.0568 24.4608 54.3998 48.4334 60.177C50.2481 60.6143 52.1215 60.4687 53.7082 59.7673C55.3708 59.0317 56.8817 57.9046 58.0783 56.5054L59.574 54.755C60.6869 53.454 60.6735 51.551 59.5421 50.2301C56.2623 46.3982 52.6957 43.2348 48.9429 40.8279C47.5947 39.9633 45.8609 40.0955 44.7282 41.1473C43.9561 41.8649 43.1762 42.6708 42.4105 43.5437C41.1261 45.0079 39.015 45.4439 37.2762 44.6038C28.8069 40.5107 22.4374 34.141 18.3445 25.671C17.504 23.9325 17.94 21.8211 19.4042 20.5367C20.2744 19.7733 21.0811 18.9934 21.801 18.2189C22.8538 17.0862 22.9846 15.3533 22.12 14.0047C19.7131 10.251 16.5501 6.68443 12.7178 3.40503C12.0511 2.83358 11.2355 2.54739 10.4236 2.54739L10.4238 2.54752Z"
                        fill="#009146" />
                  </svg>
               </span>
               <h4>Phone</h4>
               <p><a href="tel:917777453091">+917777453091</a> <br><a href="tel:123-456-7890">123-456-7890</a></p>
            </div>
         </div>
         <div class="contact-form-wrapper">
            <div class="contact-form">
               <h4>Contact Us</h4>
               <form class="myform">
                  <div class="row">
                     <div class="col-md-12 col-12">
                        <div class="form-group">
                           <label>Name</label>
                           <input type="text" class="form-control require" name="first_name">
                        </div>
                     </div>
                     <div class="col-md-12 col-12">
                        <div class="form-group">
                           <label>Your Email</label>
                           <input type="text" class="form-control require" name="email" data-valid="email"
                              data-error="Email should be valid.">
                        </div>
                     </div>
                     <div class="col-md-12 col-12">
                        <div class="form-group">
                           <label>Message</label>
                           <textarea cols="30" rows="5" class="form-control require" name="message"></textarea>
                        </div>
                     </div>
                  </div>
                  <div class="response"></div>
                  <input type="hidden" name="form_type" value="contact" />
                  <button type="button" class="submitForm redButton"><span>Send Now</span></button>
               </form>
            </div>
            <div class="contact-location">
               <!-- <div class="mapouter"><div class="gmap_canvas"><iframe width="100%" height="100%" id="gmap_canvas" src="https://maps.google.com/maps?q=dewas&t=&z=10&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://2yu.co">2yu</a><br><style>.mapouter{position:relative;text-align:right;height:100%;width:100%;}</style><a href="https://embedgooglemap.2yu.co/">html embed google map</a><style>.gmap_canvas {overflow:hidden;background:none!important;height:100%;width:100%;}</style></div></div> -->
               <iframe
                  src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d14694.691991830698!2d76.0507944!3d22.96226705!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sin!4v1682761860904!5m2!1sen!2sin"
                  style="border:0;" allowfullscreen="" loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"></iframe>
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
?>