<?php
define('TITLE', 'Home');
//--------------------------------------------------
include "dbsetting/lms_vars_config.php";
include "dbsetting/classdbconection.php";
$dblms = new dblms();
include("include/header.php");

echo '

<style>


   @media (max-width: 1920px){

      .slider-img{
         width: 52% !important;
      }
      .slider-content {
         width: 45% !important;
      }      
   }
   @media (max-width: 1707px){
      .slider-img{
         width: 50% !important;
      }
      .slider-content {
         width: 50% !important;
      }      
   }
   @media (max-width: 1280px){
      .slider-img{
         width: 50% !important;
      }
      .slider-content {
         width: 45% !important;
      }      
   }
   @media (max-width: 1024px){
      .slider-content {
         width: 80% !important;
      } 
   }

</style>
   <div class="ibadat-home-sec-banner-wrapper" style="background: #279249;">
      <div class="container custom-container">
         <div class="home-sec-slider">
            <div class="owl-carousel owl-theme">
               <div class="item">
                  <div class="home-sec-inner-slider">
                     <div class="slider-img">
                        <img src="assets/images/slider/01.png" alt="img">
                     </div>
                     <div class="slider-content">                           
                     <h2>Quran Translation</h2>
                     <h4>Learn Quran Translation With Modern Scientific & Multimedia Method</h4>
                     <a class="redButton mt-2" href="class-6.php"><span>Learn More</span></a>
                     </div>
                  </div>
               </div>
               <div class="item">
                  <div class="home-sec-inner-slider">
                     <div class="slider-img">
                        <img src="assets/images/slider/02.png" alt="img">
                     </div>
                     <div class="slider-content">                           
                     <h2>Tafseer-Ul-Quran</h2>
                     <h4>Understanding Of What Quran Teaches Us </h4>
                     <a class="redButton" href="tafseer-a-quran.php"><span>Learn More</span></a>
                     </div>

                  </div>
               </div>
               <div class="item">
                  <div class="home-sec-inner-slider">
                     <div class="slider-img">
                        <img src="assets/images/slider/04.png" alt="img">
                     </div>
                     <div class="slider-content">                           
                     <h2>Arabic Grammar</h2>
                     <h4>Learning Arabic Grammar With Easy And Interactive Slides</h4>
                     <a class="redButton" href="quranic-grammer.php"><span>Learn More</span></a>
                     </div>

                  </div>
               </div>
               <div class="item">
                  <div class="home-sec-inner-slider">
                     <div class="slider-img">
                        <img src="assets/images/slider/03.png" alt="img">
                     </div>
                     <div class="slider-content">                           
                     <h2>Feham-Ul-Quran</h2>
                     <h4>Easy Way To Understand Quran And Arabic Grammar </h4>
                     <a class="redButton" href="fahem-ul-quran.php"><span>Learn More</span></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>      
   </div>
   <!--  -->

   <!-- About us -->
   <div class="ibadat-about-main-wrapper mt-5">
      <div class="container custom-container">
         <div class="ibadat-about-main-content-wrapper">
            <div class="about-text-wrappper">
               <div class="left-title">
                  <h4>About Us <span><img src="assets/images/home/line.png" alt="line"></span> </h4>
                  <h2>In The Name Of Allah The <br> Beneficent The Merciful</h2>
                  <p><strong style="font-weight: bolder;color: black;">The Teacher</strong> initiates the online reading of subjects i.e. the Quran, the Hadith, Fiqh, beliefs, heritage and so many other topics and subjects without the physical presence of a teacher, using multimedia slides. Through <strong style="font-weight: bolder;color: black;">The Teacher</strong>, the students learns online themselves without any teacher. However, instead of just listening to lecturers, the student displays each slide himself. Audio facilities will also be provided in these slides where will necessary.  </p>
               </div>
               <div class="about-mission-wrapper">
                  <div class="about-mission">
                     <div class="mission-icon-wrapper">
                        <div class="mission-icon">
                           <span>
                              <svg width="32" height="38" viewBox="0 0 32 38" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.54559e-05 1.26728V36.7327C3.54559e-05 37.4308 0.552061 38 1.23227 38C1.91247 38 2.4645 37.4308 2.4645 36.7327V21.544H13.5363C13.8627 21.544 14.1758 21.6769 14.4073 21.9154C14.6378 22.1525 14.7685 22.4755 14.7685 22.8113V25.3496C14.7685 26.0491 15.3194 26.6169 16.0007 26.6169H30.7678C31.448 26.6169 32 26.0491 32 25.3496V6.34014C32 5.63945 31.448 5.07285 30.7678 5.07285H18.4652C18.1373 5.07285 17.8243 4.93848 17.5927 4.70143C17.3622 4.46324 17.2329 4.14133 17.2329 3.80556V3.80188C17.2329 2.79312 16.8435 1.82628 16.1498 1.11399C15.4561 0.400567 14.516 0 13.5362 0H1.23223C0.552025 0 3.54559e-05 0.567718 3.54559e-05 1.26728ZM14.7868 13.9402V3.80187C14.7868 3.46609 14.6377 3.14276 14.4072 2.906C14.1756 2.66781 13.8626 2.53458 13.5362 2.53458H2.46438V19.0094H13.5362C14.5159 19.0094 15.456 19.41 16.1497 20.1234C16.8434 20.8357 17.2328 21.8026 17.2328 22.8113V24.0823H29.5354V7.60745H18.4651C18.0473 7.60745 17.6383 7.53391 17.2513 7.39586V13.9402C17.2513 14.6398 16.6993 15.2075 16.0191 15.2075C15.3389 15.2075 14.7868 14.6398 14.7868 13.9402Z"
                                    fill="white" />
                              </svg>
                           </span>
                        </div>
                        <div class="mission-text">
                           <h4>Our Mission</h4>
                        </div>
                     </div>
                     <div class="mission-content">
                       <p>The fundamental purpose of <strong style="font-weight: bolder;color: black;">The Teacher</strong> is to make ancient Islamic sciences and arts more accessible and understandable to society through the best, simple, and multimedia teaching methods.</p>
                     </div>
                  </div>
                  <div class="about-mission">
                     <div class="mission-icon-wrapper">
                        <div class="mission-icon">
                           <span>
                              <svg width="30" height="26" viewBox="0 0 42 26" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M21.0012 0.000888475C12.3635 0.00123846 4.39284 4.73538 0.158458 12.3782H0.158803C-0.0528192 12.7596 -0.0528192 13.2257 0.158113 13.6075C4.38852 21.2568 12.3601 25.9964 21.0009 26V25.9996H21.0023C29.64 25.9965 37.6098 21.2596 41.8415 13.6143C42.0528 13.2329 42.0528 12.7668 41.8415 12.3854C37.6097 4.74022 29.6396 0.00447901 21.0023 0H21.0012V0.000888475ZM21.0009 2.50913C28.5167 2.51193 35.459 6.50351 39.3334 13.0008C35.4583 19.4974 28.5167 23.4879 21.0018 23.4915C13.4836 23.4887 6.53924 19.4943 2.66486 12.9936C6.54233 6.49903 13.4851 2.50913 21 2.50913H21.0009ZM20.9999 5.39702C16.8783 5.39702 13.5106 8.81611 13.5106 13.0005C13.5106 17.1845 16.8783 20.6036 20.9999 20.6036C25.121 20.6036 28.4888 17.1845 28.4888 13.0005C28.4888 8.81611 25.121 5.39702 20.9999 5.39702ZM20.9999 7.90526C23.7858 7.90526 26.0179 10.1714 26.0179 13.0001C26.0179 15.8285 23.7858 18.0946 20.9999 18.0946C18.2136 18.0946 15.9815 15.8285 15.9815 13.0001C15.9815 10.1714 18.2136 7.90526 20.9999 7.90526Z"
                                    fill="white" />
                              </svg>
                           </span>
                        </div>
                        <div class="mission-text">
                           <h4>Our Vision</h4>
                        </div>
                     </div>
                     <div class="mission-content">
                        <p>The primary goal of <strong style="font-weight: bolder;color: black;">The Teacher\'s</strong> teaching method is to teach the translation of the Quran in such a simple and easy way that even a student in the primary class can understand the translation of the Holy Quran.</p>
                     </div>
                  </div>
               </div>
               <a class="redButton" href="Aboutus.php"> <span>Read More</span> </a>
            </div>
            <div class="about-img-wrapper">
               <div class="circle-img">
                  <img src="assets/images/home/circle.png" alt="">
                  <div class="img-bless">
                     <img src="assets/images/featureswithimages/ghulam_murtaza.png" alt="">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>


   <!--    Features      -->
   <div class="fi-section_2">
		<div class="section2_img_overlay"></div>
		<div class="container feature_img-head">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">
					<div class="text_wrapper_list">
						<h2 class="text-center">Our Features List</h2>
						<p class="text-center">Benefits of The Teachers.</p>
					</div>
				</div>
				<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12">
					<div class="text_wrapper_left">
						<div class="icon_wrapper_list">
							<div class="icon_content_left">
								<h4><a href="#">Multi-Method Quran Understanding</a></h4>
								<p>Offers 22 methods for Quran understanding with translations in Urdu and English</p>
							</div>
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-desktop" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						<div class="icon_wrapper_list">
							<div class="icon_content_left">
								<h4><a href="#">Simplified Arabic Grammar Lessons</a></h4>
								<p>Simplifies complex Arabic grammar using animations and presentations, making it understandable.</p>
							</div>
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-chalkboard-teacher" aria-hidden="true"></i>
								</div>
							</div>
						</div>
						<div class="icon_wrapper_list">
							<div class="icon_content_left">
								<h4><a href="#">Renowned Reciters\' Quranic<br/>Audio</a></h4>
								<p>Includes Urdu translation in the voice of Tassleem Ahmed Sabri.</p>
							</div>
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-headphones" aria-hidden="true"></i>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 col-md-4 col-xs-12 d-lg-block d-md-none d-sm-none d-xs-none">
					<div class="section2_img_wrapper">
						<img class="img-fluid" src="assets/images/featureswithimages/section_2.png" alt="section-2-img">
					</div>
				</div>
				<div class="col-lg-4 col-md-6 col-xs-12 col-sm-12">
					<div class="text_wrapper_right">
						<div class="icon_wrapper_list">
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-volume-up" aria-hidden="true"></i>
								</div>
							</div>
							<div class="icon_content_right">
								<h4><a href="#">Urdu Translation by Tassleem Ahmed Sabri</a></h4>
								<p>Features Quranic verses with audio from renowned reciters.</p>
							</div>
						</div>
						<div class="icon_wrapper_list">
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-align-center"></i>
								</div>
							</div>
							<div class="icon_content_right">
								<h4><a href="#">Punjab Textbook Board Syllabus Alignment</a></h4>
								<p>Aligns with Punjab Textbook Board syllabus for grades 6-12, ensuring over 50% comprehension through presentations alone.</p>
							</div>
						</div>
						<div class="icon_wrapper_list">
							<div class="icon_img_effect">
								<div class="icon_img_list">
									<i class="fas fa-user-graduate" aria-hidden="true"></i>
								</div>
							</div>
							<div class="icon_content_right">
								<h4><a href="#">Lifetime Adoption by Teachers</a></h4>
								<p>Teachers using this method will likely adopt it for life due to its effectiveness.</p>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-sm-12 d-lg-none d-md-block d-sm-block d-xs-block">
					<div class="section2_img_wrapper">
						<img class="img-fluid" src="images/featureswithimages/section_2.png" alt="section-2-img">
					</div>
				</div>
			</div>
		</div>
	</div>

   <!-- Audio -->
   <div class="ibadat-audio-home-sec-wrapper">
      <div class="container custom-container">
         <div class="ibadat-audio-wrapper">
            <div class="book-img">
               <img src="assets/images/home/book.png" alt="book">
            </div>
            <div class="book-text">
               <a class="greenButton" href="javascript:;">Now Playing</a>
               <h2>Listen To Quran Audio</h2>
               <p>Surah Fath (Abd-ur Rahman)</p>
               <audio controls>
                  <!-- <source src="horse.ogg" type="audio/ogg"> -->
                  <source src="assets/images/home/Al-Fatihah.mp3" type="audio/mpeg">
               </audio>
            </div>
         </div>
      </div>
   </div>
   <style>
      .service-img img{
         min-height: 515px;
      }
      
      .service-img img{
         max-height: 535px;
      }

      .service-content {         
         min-height: 138px;
      }
   </style>
   <!-- our service -->
   <div class="ibadat-home-sec-srvices-wrapper">
      <div class="container custom-container">
         <div class="home-sec-title">
            <h4>Our Services</h4>
            <h2>Cousese We Provide</h2>
         </div>
         <div class="ibadat-main-service-inner-wrapper">
            <div class="service-box">
               <div class="service-img">
                  <img src="assets/images/home/class-6.jpg" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-6.php">Quran Translation For Class-6</a></h4>
               </div>
            </div>
            <div class="service-box">
               <div class="service-img">
                  <img src="assets/images/home/class-7.jpeg" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-7.php">Quran Translation For Class-7</a></h4>
               </div>
            </div>
            <div class="service-box">
               <div class="service-img">

                  <img src="assets/images/home/class8.png" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-8.php">Quran Translation For Class-8</a></h4>
               </div>
            </div>
            <div class="service-box">
               <div class="service-img">

                  <img src="assets/images/home/class-9.png" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-9.php">Quran Translation For Class-9</a></h4>
               </div>
            </div>
            <div class="service-box">
               <div class="service-img">

                  <img src="assets/images/home/class-10.jpg" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-10.php">Quran Translation For Class-10</a></h4>
               </div>
            </div>
            <div class="service-box">
               <div class="service-img">

                  <img src="assets/images/home/class-11.webp" alt="img">
               </div>
               <div class="service-content">
                  <h4><a href="class-11.php">Quran Translation For Class-11</a></h4>
               </div>
            </div>
         </div>
      </div>
   </div>


   <!--    Objectives      -->
   <div class="al-section-3 w-100 pb-5" style="background-color: #f8f2e8;">
      <div class="container" style="max-width: 1490px;">
         <div class="ibadat-islamic-praying-main-wrapper">
            <div class="container custom-container">
               <div class="center-title">
                  <h4>Objectives</h4>
                  <h2>The Teacher\'s Objectives</h2>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <div class="alert-section-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                  <div class="alert alert-light alert-dismissible fade show bullhorn-color" role="alert">
                     <div class="alert-box">
                        <div class="alert-img">
                           <span class="alert-icon">
                              <img width="50" height="50" src="assets/images/icons/icons8-learning-50.png" alt="icons8-learning"/>
                           </span>
                        </div>
                        <div class="alert-content">
                           <h4>Easy To Learn.</h4>
                           <p>Teach Quran translation so simply that even primary students can understand it.
                           </p>
                        </div>
                     </div>
                  </div>
               </div>               
               <div class="alert-section-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                  <div class="alert alert-light alert-dismissible fade show triangle-color" role="alert">
                     <div class="alert-box">
                        <div class="alert-img">
                           <span class="alert-icon">
                              <img width="40" height="40" src="assets/images/icons/icons8-agree-48.png" alt="icons8-agree"/>
                           </span>
                        </div>
                        <div class="alert-content">
                           <h4>Reviving Quran In Society</h4>
                           <p>End the trend of reciting the Quran without translation, especially in Pakistan.
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="alert-section-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                  <div class="alert alert-light alert-dismissible fade show umbrella-color" role="alert">
                     <div class="alert-box">
                        <div class="alert-img">
                           <span class="alert-icon">
                              <img width="50" height="50" src="assets/images/icons/icons8-quran-50.png" alt="icons8-quran"/>
                           </span>
                        </div>
                        <div class="alert-content">
                           <h4>Understanding Of Quran.</h4>
                           <p>Ensure every individual comprehensively understands each word and its intent in the Quran.

                           </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-6 col-md-12 col-sm-12 col-12">
               <div class="alert-section-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                  <div class="alert alert-light alert-dismissible fade show bell-color" role="alert" style="background: #b7b19f;">
                     <div class="alert-box">
                        <div class="alert-img">                           
                           <span class="alert-icon">
                              <img width="50" height="50" src="assets/images/icons/icons8-leadership-50.png" alt="icons8-leadership"/>
                           </span>
                        </div>
                        <div class="alert-content">
                           <h4>Leadership Building.</h4>
                           <p>Adapt Quranic teachings to shape students\' character for global guidance.
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="alert-section-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
                  <div class="alert alert-light alert-dismissible fade show check-color" role="alert">
                     <div class="alert-box">
                        <div class="alert-img">
                           <span class="alert-icon">
                              <img width="50" height="50" src="assets/images/icons/icons8-knowledge-sharing-50.png" alt="willingness-to-learn"/>
                           </span>
                        </div>
                        <div class="alert-content">
                           <h4>Importance Of Understanding Quran</h4>
                           <p>Promote Quran translation alongside memorization in religious education, requiring it for Shahadah al-‘Alamiyyah degrees.
                           </p>
                        </div>
                     </div>
                  </div>
               </div>               
            </div>
         </div>
      </div>
   </div>




   <!-- Testimonial client -->
   <div class="ibadat-home-sec-testimonial-wrapper">
      <div class="container custom-container">
         <div class="home-sec-testimonial-wrapper">
            <div class="test-content">
               <div class="home-sec-title">
                  <h4>Testimonial</h4>
                  <h2>What Our Client Say’s</h2>
               </div>
            </div>
            <div class="test-slider-wrapper">
               <div class="owl-carousel owl-theme">
                  <div class="item">
                     <div class="testi-item">
                        <div class="testi-img">
                           <img src="assets/images/home2/testi.png" alt="img">
                        </div>
                        <div class="testi-text">
                           <p>
                              There are many variations of passages of Lorem Ipsum majority have alteration in some
                              form, by injected humour, or randomised which to
                              look even slightly believable. There are many variations of pas Lorem Ipsum majority have
                              suffered alteration in some form, by injecumour,
                              or randomised which don\'t look even slightly believable.
                           </p>
                           <h4>
                              <a href="javascript:;">David Lathem</a>
                           </h4>
                           <p>CEO & Founder</p>
                           <ul>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.22374 0.652909L7.22374 0.652897C7.12743 0.448832 6.87225 0.449298 6.77635 0.652712L6.77626 0.652909L5.19725 3.99847C5.01941 4.37533 4.6696 4.64728 4.25247 4.71059C4.25243 4.7106 4.25238 4.71061 4.25234 4.71061L0.721496 5.24712L0.721311 5.24715C0.626706 5.26149 0.549444 5.32727 0.515194 5.4375C0.480714 5.54847 0.504102 5.6642 0.58539 5.74716C0.585418 5.74718 0.585445 5.74721 0.585472 5.74724L3.14039 8.35132L3.14049 8.35143C3.4322 8.64892 3.56079 9.07113 3.49358 9.48017C3.49356 9.48025 3.49355 9.48034 3.49354 9.48042L2.89053 13.1574L2.89052 13.1575C2.86916 13.2876 2.92189 13.3905 2.99954 13.4495C3.07516 13.507 3.16304 13.5174 3.25016 13.4695L3.25022 13.4695L6.40826 11.7335L6.40842 11.7334C6.77793 11.5305 7.22207 11.5305 7.59158 11.7334L7.59174 11.7335L10.7497 13.4694C10.7497 13.4695 10.7497 13.4695 10.7498 13.4695C10.8369 13.5173 10.9249 13.5068 11.0005 13.4494C11.0781 13.3904 11.1308 13.2876 11.1095 13.1575L11.1095 13.1574L10.5065 9.48042C10.5065 9.48039 10.5065 9.48036 10.5064 9.48033C10.5064 9.48027 10.5064 9.48022 10.5064 9.48017C10.4392 9.07113 10.5678 8.64892 10.8595 8.35143L10.8596 8.35132L13.4146 5.74716L13.4146 5.74712C13.4958 5.66437 13.5193 5.54876 13.4848 5.43773C13.4505 5.32748 13.3732 5.26154 13.2785 5.24712L7.22374 0.652909ZM7.22374 0.652909L8.80275 3.99847C8.80276 3.99848 8.80276 3.99849 8.80276 3.9985C8.98061 4.37535 9.33041 4.64728 9.74753 4.71059C9.74758 4.7106 9.74762 4.71061 9.74766 4.71061L13.2785 5.24711L7.22374 0.652909Z"
                                       stroke="#FAB257" />
                                 </svg>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="item">
                     <div class="testi-item">
                        <div class="testi-img">
                           <img src="assets/images/home2/testi1.png" alt="img">
                        </div>
                        <div class="testi-text">
                           <p>
                              There are many variations of passages of Lorem Ipsum majority have alteration in some
                              form, by injected humour, or randomised which to
                              look even slightly believable. There are many variations of pas Lorem Ipsum majority have
                              suffered alteration in some form, by injecumour,
                              or randomised which don\'t look even slightly believable.
                           </p>
                           <h4>
                              <a href="javascript:;">Saleena Sheikh</a>
                           </h4>
                           <p>Manager</p>
                           <ul>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.22374 0.652909L7.22374 0.652897C7.12743 0.448832 6.87225 0.449298 6.77635 0.652712L6.77626 0.652909L5.19725 3.99847C5.01941 4.37533 4.6696 4.64728 4.25247 4.71059C4.25243 4.7106 4.25238 4.71061 4.25234 4.71061L0.721496 5.24712L0.721311 5.24715C0.626706 5.26149 0.549444 5.32727 0.515194 5.4375C0.480714 5.54847 0.504102 5.6642 0.58539 5.74716C0.585418 5.74718 0.585445 5.74721 0.585472 5.74724L3.14039 8.35132L3.14049 8.35143C3.4322 8.64892 3.56079 9.07113 3.49358 9.48017C3.49356 9.48025 3.49355 9.48034 3.49354 9.48042L2.89053 13.1574L2.89052 13.1575C2.86916 13.2876 2.92189 13.3905 2.99954 13.4495C3.07516 13.507 3.16304 13.5174 3.25016 13.4695L3.25022 13.4695L6.40826 11.7335L6.40842 11.7334C6.77793 11.5305 7.22207 11.5305 7.59158 11.7334L7.59174 11.7335L10.7497 13.4694C10.7497 13.4695 10.7497 13.4695 10.7498 13.4695C10.8369 13.5173 10.9249 13.5068 11.0005 13.4494C11.0781 13.3904 11.1308 13.2876 11.1095 13.1575L11.1095 13.1574L10.5065 9.48042C10.5065 9.48039 10.5065 9.48036 10.5064 9.48033C10.5064 9.48027 10.5064 9.48022 10.5064 9.48017C10.4392 9.07113 10.5678 8.64892 10.8595 8.35143L10.8596 8.35132L13.4146 5.74716L13.4146 5.74712C13.4958 5.66437 13.5193 5.54876 13.4848 5.43773C13.4505 5.32748 13.3732 5.26154 13.2785 5.24712L7.22374 0.652909ZM7.22374 0.652909L8.80275 3.99847C8.80276 3.99848 8.80276 3.99849 8.80276 3.9985C8.98061 4.37535 9.33041 4.64728 9.74753 4.71059C9.74758 4.7106 9.74762 4.71061 9.74766 4.71061L13.2785 5.24711L7.22374 0.652909Z"
                                       stroke="#FAB257" />
                                 </svg>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
                  <div class="item">
                     <div class="testi-item">
                        <div class="testi-img">
                           <img src="assets/images/home2/testi2.png" alt="img">
                        </div>
                        <div class="testi-text">
                           <p>
                              There are many variations of passages of Lorem Ipsum majority have alteration in some
                              form, by injected humour, or randomised which to
                              look even slightly believable. There are many variations of pas Lorem Ipsum majority have
                              suffered alteration in some form, by injecumour,
                              or randomised which don\'t look even slightly believable.
                           </p>
                           <h4>
                              <a href="javascript:;">Fatime Ali</a>
                           </h4>
                           <p>Designer</p>
                           <ul>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.67591 0.439498L9.25493 3.78509C9.36474 4.01778 9.57705 4.17901 9.82265 4.21627L13.3536 4.7528C13.9721 4.84685 14.2189 5.64141 13.7715 6.09732L11.2165 8.70149C11.039 8.88257 10.9578 9.14366 10.9998 9.39925L11.6029 13.0765C11.7086 13.7205 11.062 14.2115 10.5089 13.9077L7.35088 12.1717C7.13127 12.051 6.86873 12.051 6.64912 12.1717L3.49108 13.9077C2.93797 14.2118 2.29141 13.7205 2.39712 13.0765L3.00017 9.39925C3.04222 9.14366 2.96104 8.88257 2.78348 8.70149L0.228484 6.09732C-0.218911 5.64111 0.0278572 4.84654 0.646383 4.7528L4.17735 4.21627C4.42295 4.17901 4.63526 4.01778 4.74507 3.78509L6.32409 0.439498C6.60035 -0.146499 7.39936 -0.146499 7.67591 0.439498Z"
                                       fill="#FAB257" />
                                 </svg>
                              </li>
                              <li>
                                 <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                       d="M7.22374 0.652909L7.22374 0.652897C7.12743 0.448832 6.87225 0.449298 6.77635 0.652712L6.77626 0.652909L5.19725 3.99847C5.01941 4.37533 4.6696 4.64728 4.25247 4.71059C4.25243 4.7106 4.25238 4.71061 4.25234 4.71061L0.721496 5.24712L0.721311 5.24715C0.626706 5.26149 0.549444 5.32727 0.515194 5.4375C0.480714 5.54847 0.504102 5.6642 0.58539 5.74716C0.585418 5.74718 0.585445 5.74721 0.585472 5.74724L3.14039 8.35132L3.14049 8.35143C3.4322 8.64892 3.56079 9.07113 3.49358 9.48017C3.49356 9.48025 3.49355 9.48034 3.49354 9.48042L2.89053 13.1574L2.89052 13.1575C2.86916 13.2876 2.92189 13.3905 2.99954 13.4495C3.07516 13.507 3.16304 13.5174 3.25016 13.4695L3.25022 13.4695L6.40826 11.7335L6.40842 11.7334C6.77793 11.5305 7.22207 11.5305 7.59158 11.7334L7.59174 11.7335L10.7497 13.4694C10.7497 13.4695 10.7497 13.4695 10.7498 13.4695C10.8369 13.5173 10.9249 13.5068 11.0005 13.4494C11.0781 13.3904 11.1308 13.2876 11.1095 13.1575L11.1095 13.1574L10.5065 9.48042C10.5065 9.48039 10.5065 9.48036 10.5064 9.48033C10.5064 9.48027 10.5064 9.48022 10.5064 9.48017C10.4392 9.07113 10.5678 8.64892 10.8595 8.35143L10.8596 8.35132L13.4146 5.74716L13.4146 5.74712C13.4958 5.66437 13.5193 5.54876 13.4848 5.43773C13.4505 5.32748 13.3732 5.26154 13.2785 5.24712L7.22374 0.652909ZM7.22374 0.652909L8.80275 3.99847C8.80276 3.99848 8.80276 3.99849 8.80276 3.9985C8.98061 4.37535 9.33041 4.64728 9.74753 4.71059C9.74758 4.7106 9.74762 4.71061 9.74766 4.71061L13.2785 5.24711L7.22374 0.652909Z"
                                       stroke="#FAB257" />
                                 </svg>
                              </li>
                           </ul>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   
   <!-- subscribe -->
   <div class="ibadat-subcribe-main-wrapper">
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
