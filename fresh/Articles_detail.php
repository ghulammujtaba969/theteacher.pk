<?php
define('TITLE', 'Article Detail');
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
            <h2>Blog Single</h2>
            <ul>
               <li><a href="javascript:;">Home</a></li>
               <li><a href="javascript:;">| &nbsp; Blog Single</a></li>
            </ul>
         </div>
      </div>
   </div>
   <!--  -->
   <div class="ibadat-event-text-main-wrapper">
      <div class="container custom-container">
         <div class="event-inner-main-wraper">
            <div class="blog-single-main-wrapper">
               <div class="event-single-blog-wrapper wow fadeInUp" data-wow-delay="100ms">
                  <div class="event-img-wrapper">
                     <img src="images/home/blog1.png" alt="img">
                  </div>
                  <div class="event-text-wrapper">
                     <div class="date-wrapper">
                        <span>
                           <svg width="20" height="20" viewBox="0 0 23 23" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                 d="M19.3241 2.27318H17.0507V1.13647C17.0507 0.834993 16.9309 0.545866 16.7177 0.332691C16.5046 0.119516 16.2154 -0.000244141 15.914 -0.000244141C15.6125 -0.000244141 15.3234 0.119516 15.1102 0.332691C14.897 0.545866 14.7773 0.834993 14.7773 1.13647V2.27318H7.95698V1.13647C7.95698 0.834993 7.83722 0.545866 7.62405 0.332691C7.41087 0.119516 7.12175 -0.000244141 6.82027 -0.000244141C6.5188 -0.000244141 6.22967 0.119516 6.01649 0.332691C5.80332 0.545866 5.68356 0.834993 5.68356 1.13647V2.27318H3.41014C2.50571 2.27318 1.63833 2.63246 0.998806 3.27199C0.359281 3.91151 0 4.77889 0 5.68332V19.3239C0 20.2283 0.359281 21.0957 0.998806 21.7352C1.63833 22.3747 2.50571 22.734 3.41014 22.734H19.3241C20.2285 22.734 21.0959 22.3747 21.7354 21.7352C22.375 21.0957 22.7342 20.2283 22.7342 19.3239V5.68332C22.7342 4.77889 22.375 3.91151 21.7354 3.27199C21.0959 2.63246 20.2285 2.27318 19.3241 2.27318ZM20.4608 19.3239C20.4608 19.6253 20.3411 19.9145 20.1279 20.1276C19.9147 20.3408 19.6256 20.4606 19.3241 20.4606H3.41014C3.10866 20.4606 2.81953 20.3408 2.60636 20.1276C2.39318 19.9145 2.27342 19.6253 2.27342 19.3239V11.3669H20.4608V19.3239ZM20.4608 9.09345H2.27342V5.68332C2.27342 5.38184 2.39318 5.09271 2.60636 4.87954C2.81953 4.66636 3.10866 4.5466 3.41014 4.5466H5.68356V5.68332C5.68356 5.98479 5.80332 6.27392 6.01649 6.48709C6.22967 6.70027 6.5188 6.82003 6.82027 6.82003C7.12175 6.82003 7.41087 6.70027 7.62405 6.48709C7.83722 6.27392 7.95698 5.98479 7.95698 5.68332V4.5466H14.7773V5.68332C14.7773 5.98479 14.897 6.27392 15.1102 6.48709C15.3234 6.70027 15.6125 6.82003 15.914 6.82003C16.2154 6.82003 16.5046 6.70027 16.7177 6.48709C16.9309 6.27392 17.0507 5.98479 17.0507 5.68332V4.5466H19.3241C19.6256 4.5466 19.9147 4.66636 20.1279 4.87954C20.3411 5.09271 20.4608 5.38184 20.4608 5.68332V9.09345Z"
                                 fill="#009146"></path>
                           </svg>
                           &nbsp; 02 July 2023
                        </span>
                        <span>
                           <svg width="22" height="22" viewBox="0 0 26 26" fill="none"
                              xmlns="http://www.w3.org/2000/svg">
                              <path
                                 d="M12.6405 25.0077C12.3391 25.0077 12.0499 24.8879 11.8368 24.6747C11.6236 24.4616 11.5038 24.1724 11.5038 23.8709V20.4608H6.95699C6.35404 20.4608 5.77579 20.2213 5.34944 19.7949C4.92309 19.3686 4.68357 18.7903 4.68357 18.1874V6.82027C4.68357 6.21732 4.92309 5.63907 5.34944 5.21272C5.77579 4.78637 6.35404 4.54685 6.95699 4.54685H22.871C23.4739 4.54685 24.0522 4.78637 24.4785 5.21272C24.9049 5.63907 25.1444 6.21732 25.1444 6.82027V18.1874C25.1444 18.7903 24.9049 19.3686 24.4785 19.7949C24.0522 20.2213 23.4739 20.4608 22.871 20.4608H18.2104L14.0046 24.678C13.7773 24.894 13.4931 25.0077 13.2089 25.0077H12.6405ZM13.7773 18.1874V21.6885L17.2783 18.1874H22.871V6.82027H6.95699V18.1874H13.7773ZM2.41014 15.914H0.136719V2.27342C0.136719 1.67047 0.37624 1.09222 0.802589 0.66587C1.22894 0.239521 1.80719 0 2.41014 0H20.5975V2.27342H2.41014V15.914Z"
                                 fill="#009146" />
                           </svg>
                           &nbsp; 03 Comments
                        </span>
                     </div>
                     <h4>Ramzan Arrangement</h4>
                     <p>Proin gravida nibh vel velit auctor aliquet. Aenean itudin, lorem quis bibendum auctor, nisi elt
                        consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a
                        sit amet mauris. Nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum uctor,
                        nisi elit consequat ipsum, nec sagittis sem nibh id elit.Proin gravida nibh vel velit auctor
                        aliquet. Aenean are itudin, lorem quis bibendum auctor, nisi elt consequat ipsum, nec sagittis
                        sem nibh id elit. Duis sed odio sit amet nibh vulputate is cursus a sit amet mauris. Nibh vel
                        velit auctor aliquet.</p>
                     <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum
                        deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non
                        provident, similiquesunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum
                        fuga. Et harum quidem rerum facilis est et expedita distinctio.</p>
                     <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum
                        deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non
                        provident, similiquesunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum
                        fuga. Et harum quidem rerum facilis est et expedita distinctio.</p>
                  </div>
               </div>
               <div class="client-say">
                  <p>Continually whiteboard enterprise-wide vortals whereas world-class <br>
                     resources. Quickly brand team building services via functionalized users.
                  </p>
                  <h2> Abdul Ghani Azhari</h2>
                  <div class="quotes-img">
                     <img src="images/home/quote.png" alt="img">
                  </div>
               </div>
               <div class="blog-footer-wrapper">
                  <div class="b-footer-details">
                     <h4>Tags:</h4>
                     <div class="cat action">
                        <label>
                           <input type="checkbox" value="1"><span>Community</span>
                        </label>
                        <label>
                           <input type="checkbox" value="1"><span>Religion</span>
                        </label>
                     </div>
                  </div>
                  <div class="b-footer-details">
                     <h4>Share</h4>
                     <ul>
                        <li>
                           <a href="javascript:;">
                              <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path
                                    d="M15 7.5188C15 3.36842 11.64 0 7.5 0C3.36 0 0 3.36842 0 7.5188C0 11.1579 2.58 14.188 6 14.8872V9.77444H4.5V7.5188H6V5.6391C6 4.18797 7.1775 3.00752 8.625 3.00752H10.5V5.26316H9C8.5875 5.26316 8.25 5.6015 8.25 6.01504V7.5188H10.5V9.77444H8.25V15C12.0375 14.6241 15 11.4211 15 7.5188Z"
                                    fill="" />
                              </svg>
                           </a>
                        </li>
                        <li>
                           <a href="javascript:;">
                              <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path
                                    d="M4.06 0H9.94C12.18 0 14 1.82 14 4.06V9.94C14 11.0168 13.5723 12.0495 12.8109 12.8109C12.0495 13.5723 11.0168 14 9.94 14H4.06C1.82 14 0 12.18 0 9.94V4.06C0 2.98322 0.427749 1.95054 1.18915 1.18915C1.95054 0.427749 2.98322 0 4.06 0ZM3.92 1.4C3.25165 1.4 2.61068 1.6655 2.13809 2.13809C1.6655 2.61068 1.4 3.25165 1.4 3.92V10.08C1.4 11.473 2.527 12.6 3.92 12.6H10.08C10.7483 12.6 11.3893 12.3345 11.8619 11.8619C12.3345 11.3893 12.6 10.7483 12.6 10.08V3.92C12.6 2.527 11.473 1.4 10.08 1.4H3.92ZM10.675 2.45C10.9071 2.45 11.1296 2.54219 11.2937 2.70628C11.4578 2.87038 11.55 3.09294 11.55 3.325C11.55 3.55706 11.4578 3.77962 11.2937 3.94372C11.1296 4.10781 10.9071 4.2 10.675 4.2C10.4429 4.2 10.2204 4.10781 10.0563 3.94372C9.89219 3.77962 9.8 3.55706 9.8 3.325C9.8 3.09294 9.89219 2.87038 10.0563 2.70628C10.2204 2.54219 10.4429 2.45 10.675 2.45ZM7 3.5C7.92826 3.5 8.8185 3.86875 9.47487 4.52513C10.1313 5.1815 10.5 6.07174 10.5 7C10.5 7.92826 10.1313 8.8185 9.47487 9.47487C8.8185 10.1313 7.92826 10.5 7 10.5C6.07174 10.5 5.1815 10.1313 4.52513 9.47487C3.86875 8.8185 3.5 7.92826 3.5 7C3.5 6.07174 3.86875 5.1815 4.52513 4.52513C5.1815 3.86875 6.07174 3.5 7 3.5ZM7 4.9C6.44305 4.9 5.9089 5.12125 5.51508 5.51508C5.12125 5.9089 4.9 6.44305 4.9 7C4.9 7.55695 5.12125 8.0911 5.51508 8.48492C5.9089 8.87875 6.44305 9.1 7 9.1C7.55695 9.1 8.0911 8.87875 8.48492 8.48492C8.87875 8.0911 9.1 7.55695 9.1 7C9.1 6.44305 8.87875 5.9089 8.48492 5.51508C8.0911 5.12125 7.55695 4.9 7 4.9Z"
                                    fill="" />
                              </svg>
                           </a>
                        </li>
                        <li>
                           <a href="javascript:;">
                              <svg width="15" height="13" viewBox="0 0 15 13" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path
                                    d="M15 1.52941C14.4479 1.79706 13.8528 1.97294 13.2361 2.05706C13.8671 1.65176 14.3547 1.00941 14.5841 0.237059C13.989 0.619412 13.3294 0.887059 12.6338 1.04C12.0674 0.382353 11.2715 0 10.3681 0C8.68308 0 7.30641 1.46824 7.30641 3.28059C7.30641 3.54059 7.33509 3.79294 7.38528 4.03C4.8327 3.89235 2.55975 2.58471 1.04685 0.604118C0.781549 1.08588 0.630975 1.65176 0.630975 2.24824C0.630975 3.38765 1.16874 4.39706 2.00048 4.97059C1.4914 4.97059 1.01816 4.81765 0.602295 4.58824V4.61118C0.602295 6.20176 1.66348 7.53235 3.06883 7.83059C2.61763 7.96228 2.14395 7.9806 1.68499 7.88412C1.87974 8.53601 2.26114 9.10643 2.77559 9.51518C3.29003 9.92393 3.91165 10.1505 4.55306 10.1629C3.4658 11.0809 2.11807 11.5771 0.731358 11.57C0.487572 11.57 0.243786 11.5547 0 11.5241C1.36233 12.4571 2.98279 13 4.71797 13C10.3681 13 13.4728 7.99882 13.4728 3.66294C13.4728 3.51765 13.4728 3.38 13.4656 3.23471C14.0679 2.77588 14.5841 2.19471 15 1.52941Z"
                                    fill="" />
                              </svg>
                           </a>
                        </li>
                        <li>
                           <a href="javascript:;">
                              <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                 <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.2076 8.38951C10.033 8.30259 9.17662 7.88376 9.01719 7.82543C8.85776 7.76768 8.7417 7.73909 8.62506 7.91293C8.509 8.08559 8.17549 8.47643 8.07409 8.59193C7.9721 8.70801 7.87069 8.72201 7.69661 8.63568C7.52253 8.54818 6.961 8.36559 6.29574 7.77526C5.77817 7.3156 5.42825 6.74801 5.32685 6.57418C5.22544 6.40093 5.31571 6.30701 5.40304 6.22068C5.48159 6.1431 5.57713 6.01826 5.66446 5.91735C5.7518 5.81585 5.78052 5.74351 5.83855 5.62743C5.89716 5.51193 5.86785 5.41102 5.82389 5.3241C5.78052 5.23718 5.43235 4.38377 5.28699 4.03668C5.14573 3.69893 5.00212 3.74502 4.89545 3.73918C4.79346 3.73452 4.6774 3.73335 4.56135 3.73335C4.44529 3.73335 4.25655 3.77652 4.09712 3.95035C3.93711 4.1236 3.48754 4.54302 3.48754 5.39643C3.48754 6.24926 4.11119 7.07351 4.19852 7.1896C4.28586 7.30509 5.42649 9.05626 7.17377 9.80701C7.58993 9.98551 7.91407 10.0923 8.1667 10.1716C8.58403 10.304 8.96385 10.2853 9.26395 10.2404C9.59805 10.1908 10.2944 9.82101 10.4397 9.41617C10.5845 9.01134 10.5845 8.66426 10.5412 8.59193C10.4978 8.51959 10.3817 8.47643 10.2071 8.38951H10.2076ZM7.02958 12.7079H7.02724C5.98944 12.7081 4.97069 12.4305 4.07778 11.9041L3.86677 11.7793L1.67343 12.3521L2.25899 10.2241L2.12124 10.0059C1.54104 9.08679 1.234 8.02305 1.23558 6.9376C1.23676 3.75843 3.8357 1.17194 7.03193 1.17194C8.57934 1.17194 10.0341 1.77277 11.1279 2.86244C11.6673 3.39711 12.0949 4.03293 12.3859 4.73312C12.6769 5.43332 12.8254 6.18398 12.823 6.94168C12.8218 10.1208 10.2229 12.7079 7.02958 12.7079ZM11.9602 2.03469C11.3144 1.38773 10.546 0.874752 9.6995 0.52549C8.85303 0.176227 7.94533 -0.00237781 7.029 2.39035e-05C3.18743 2.39035e-05 0.0597863 3.1121 0.0586141 6.93701C0.056834 8.15428 0.377694 9.35048 0.988819 10.4049L0 14L3.69503 13.0352C4.71719 13.5894 5.86263 13.8798 7.02665 13.8798H7.02958C10.8711 13.8798 13.9988 10.7678 14 6.94226C14.0028 6.03067 13.824 5.12757 13.4739 4.2852C13.1237 3.44283 12.6093 2.67791 11.9602 2.03469Z"
                                    fill="" />
                              </svg>
                           </a>
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="client-comment-wrapper wow fadeInUp" data-wow-delay="200ms">
                  <h4>Comments (02)</h4>
                  <div class="comment-main-wrapper">
                     <div class="comment-blog-wrapper">
                        <div class="comment-img">
                           <img src="images/home/comment1.png" alt="img">
                        </div>
                        <div class="comment-text">
                           <div class="coment-name">
                              <h4>Jhon Doe</h4>
                              <p>02 Feb 2023</p>
                           </div>
                           <p>Nor again is there anyone who loves or pursues or desires to obtain pain of itself,
                              because it is pain, but because occasionally circumstances.</p>
                           <div class="reply">
                              <a href="javascript:;">
                                 <svg width="17" height="11" viewBox="0 0 17 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M8.07984 0.000276895L0.115234 4.77133L8.07984 9.54239V6.3618C10.3823 6.3618 14.9035 7.37216 16.0444 11C16.0444 6.17912 11.9788 3.52892 8.07984 3.18087V0V0.000276895Z"
                                       fill="#009146" />
                                 </svg>
                                 Reply
                              </a>
                           </div>
                        </div>
                     </div>
                     <div class="comment-blog-wrapper">
                        <div class="comment-img">
                           <img src="images/home/comment2.png" alt="img">
                        </div>
                        <div class="comment-text">
                           <div class="coment-name">
                              <h4>Wahaj Kahn</h4>
                              <p>20 April 2023</p>
                           </div>
                           <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium
                              voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati
                              cupiditate non provident, similique</p>
                           <div class="reply">
                              <a href="javascript:;">
                                 <svg width="17" height="11" viewBox="0 0 17 11" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                       d="M8.07984 0.000276895L0.115234 4.77133L8.07984 9.54239V6.3618C10.3823 6.3618 14.9035 7.37216 16.0444 11C16.0444 6.17912 11.9788 3.52892 8.07984 3.18087V0V0.000276895Z"
                                       fill="#009146" />
                                 </svg>
                                 Reply
                              </a>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="leave-reply-form-wrapper">
                  <h4>Leave Reply</h4>
                  <div class="leave-form">
                     <form>
                        <div class="row">
                           <div class="col-md-6 col-12">
                              <div class="form-group">
                                 <label>Name</label>
                                 <input type="text" class="form-control">
                              </div>
                           </div>
                           <div class="col-md-6 col-12">
                              <div class="form-group">
                                 <label>Email</label>
                                 <input type="email" class="form-control">
                              </div>
                           </div>
                           <div class="col-md-12 col-12">
                              <div class="form-group">
                                 <label>Comment</label>
                                 <textarea cols="30" rows="10" class="form-control"></textarea>
                              </div>
                           </div>
                        </div>
                        <a class="redButton" href="javascript:;"><span>Send Now</span></a>
                     </form>
                  </div>
               </div>
            </div>
            <div class="event-blog-sidebar wow fadeInUp" data-wow-delay="300ms">
               <div class="search-box">
                  <h4>Search Your Keywrod</h4>
                  <div class="inupt-box">
                     <input type="text" class="form-control" placeholder="Search Products...">
                     <span>
                        <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                           <path
                              d="M7.49833 0.000312674C3.3688 0.000312674 0 3.36934 0 7.49864C0 11.6279 3.36902 14.997 7.49833 14.997C9.2042 14.997 10.7819 14.4241 12.0444 13.4581L17.2855 18.7073C17.676 19.0978 18.3167 19.0978 18.7072 18.7073C19.0976 18.3169 19.0976 17.684 18.7072 17.2936L13.458 12.0444C14.4236 10.782 14.9968 9.20393 14.9968 7.49833C14.9968 3.3688 11.6276 0 7.49846 0L7.49833 0.000312674ZM7.49833 1.99985C10.5472 1.99985 12.9975 4.45002 12.9975 7.49904C12.9975 10.5481 10.5473 12.9982 7.49833 12.9982C4.44931 12.9982 1.99914 10.5481 1.99914 7.49904C1.99914 4.45002 4.44931 1.99985 7.49833 1.99985Z"
                              fill="#111111" />
                        </svg>
                     </span>
                  </div>
               </div>
               <div class="recent-box">
                  <h4>Recent Posts</h4>
                  <div class="post-recent-wrapper">
                     <div class="recent-img">
                        <img src="images/home/recent1.png" alt="img">
                     </div>
                     <div class="recent-text">
                        <h4><a href="javascript:;">Amble through surahs
                              of Quran...</a>
                        </h4>
                        <p>02 September 2022</p>
                     </div>
                  </div>
                  <div class="post-recent-wrapper">
                     <div class="recent-img">
                        <img src="images/home/recent2.png" alt="img">
                     </div>
                     <div class="recent-text">
                        <h4><a href="javascript:;">Mosque of our life & our
                              salvation</a>
                        </h4>
                        <p>02 September 2022</p>
                     </div>
                  </div>
                  <div class="post-recent-wrapper">
                     <div class="recent-img">
                        <img src="images/home/recent3.png" alt="img">
                     </div>
                     <div class="recent-text">
                        <h4><a href="javascript:;">Importance of
                              prayer</a>
                        </h4>
                        <p>02 September 2022</p>
                     </div>
                  </div>
                  <div class="post-recent-wrapper">
                     <div class="recent-img">
                        <img src="images/home/recent4.png" alt="img">
                     </div>
                     <div class="recent-text">
                        <h4><a href="javascript:;">Day Of Ashura 10
                              Muharram</a>
                        </h4>
                        <p>02 September 2022</p>
                     </div>
                  </div>
               </div>
               <div class="post-category">
                  <h4>Posts Categories</h4>
                  <div class="category-list">

                     <a href="javascript:;">
                        <p>Show all</p>
                        <p>20</p>
                     </a>

                  </div>
                  <div class="category-list">
                     <a href="javascript:;">
                        <p>Business</p>
                        <p>32</p>
                     </a>

                  </div>
                  <div class="category-list">
                     <a href="javascript:;">
                        <p>Creative</p>
                        <p>11</p>
                     </a>

                  </div>
                  <div class="category-list">
                     <a href="javascript:;">
                        <p>News</p>
                        <p>28</p>
                     </a>

                  </div>
                  <div class="category-list">
                     <a href="javascript:;">
                        <p>Photography</p>
                        <p>22</p>
                     </a>

                  </div>
               </div>
               <div class="golden-hajj">
                  <h4>Golden Hajj Package</h4>
                  <img src="images/home/hajj-package.png" alt="img">
                  <a class="redButton" href="javascript:;"><span>Book Now</span></a>
               </div>
               <div class="insta-wrapper">
                  <h4>Instagram</h4>
                  <div class="insta-post-wrapper">
                     <div class="insta-img">
                        <img src="images/home/insta1.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta2.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta3.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta4.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta5.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta6.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta7.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta8.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                     <div class="insta-img">
                        <img src="images/home/insta9.png" alt="img">
                        <div class="insta-icon"><a href="javascript:;"><i class="fab fa-instagram"></i></a></div>
                     </div>
                  </div>
               </div>
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
                        d="M19.4997 21.4498C19.3577 21.4475 19.218 21.4142 19.0904 21.3522L0.000476258 12.2656V30.2248C-0.00213531 30.484 0.0997155 30.7338 0.283177 30.9172C0.466638 31.1004 0.716045 31.2022 0.975238 31.1999H38.0252C38.2844 31.2022 38.5338 31.1004 38.7173 30.9172C38.9007 30.7338 39.0026 30.484 39 30.2248V12.2656L19.91 21.3522C19.7824 21.4142 19.6427 21.4475 19.5007 21.4498H19.4997Z"
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
