<?php
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Home")) {
	$dashboardactive = 'class="active"';
} else {
	$dashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Aboutus")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$stddashboardactive = 'class="active"';
} else {
	$stdlistclsactive 	= '';
	$stdlistis		= '';
	$stddashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "contact")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$stddashboardactive = 'class="active"';
} else {
	$stdlistclsactive 	= '';
	$stdlistis		= '';
	$stddashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Students")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$stdlistactive = 'class="active"';
} else {
	$stdlistclsactive  	= '';
	$stdlistis		= '';
	$studentdashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Classes")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$Classesdashboardactive = 'class="active"';
} else {
	$stdlistclsactive 	= '';
	$stdlistis		= '';
	$Classesdashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Assignment")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$Assignmentdashboardactive = 'class="active"';
} else {
	$stdlistclsactive 	= '';
	$stdlistis		= '';
	$Assignmentdashboardactive = '';
}
//----------------------------------------------------
if ((strstr(basename($_SERVER['REQUEST_URI']), '.php', true) == "Subjects")) {
	$stdlistclsactive = 'class="subdrop"';
	$stdlistis		= 'style="display:block; visibility:visible;"';
	$Subjectsdashboardactive = 'class="active"';
} else {
	$stdlistclsactive 	= '';
	$stdlistis		= '';
	$Subjectsdashboardactive = '';
}
//----------------------------------------------------
echo '
	<div class="sb-main-header1 home-sec-header-wrapper">
		<div class="menu-items-wrapper menu-item-wrapper3 d-xl-block d-lg-block d-md-none d-sm-none d-none">
			<div class="container custom-container">
				<div class="row">
					<div class="col-lg-2 col-md-12">
						<div class="index1-logo">
						<a href="index.php">
							<img src="assets/images/logo.png" alt="logo">
						</a>
						</div>
					</div>
					<div class="col-lg-10 col-md-12">
						<div class="my-menu-header">
						<nav class="navbar navbar-expand-lg">
							<ul class="navbar-nav">
								<li class="nav-item">
									<a class="nav-link" href="Home.php">Home</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="Aboutus.php">About Us</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="javascript:;">Subjects <span><i class="fas fa-chevron-down"></i></span></a>
									<ul class="dropdown-menu">
										<li>
											<a class="dropdown-item" href="#">Quran<span><i class="fas fa-chevron-right"></i></span></a>
											<ul class="sub-dropdown">
												<li><a class="" href="class-6.php">Translation</a></li>
												<li><a class="" href="quranic-grammer.php">Quranic Grammer</a></li>
												<li><a class="" href="Dua-E-Quran.php">Dua-E-Quran</a></li>
												<li><a class="" href="Mazameen-E-Quran.php">Mazameen-E-Quran</a></li>
												<li><a class="" href="Quran-Translation-For-Schools.php">Quran Translation For Schools</a></li>
											</ul>										
										</li>
										<li>
											<a class="dropdown-item" href="coming-soon.php">Hadees</a>
										</li>
										<li>
											<a class="dropdown-item" href="coming-soon.php">Figh</a>
										</li>
										<li>
											<a class="dropdown-item" href="coming-soon.php">Belief / Aqaid</a>
										</li>
										<li>
											<a class="dropdown-item" href="coming-soon.php">Seerat</a>
										</li>
									</ul>
								</li>
								<li class="nav-item">
									<a class="nav-link" href="javascript:;">Quran Study<span><i class="fas fa-chevron-down"></i></span></a>
									<ul class="dropdown-menu">
										<li>
											<a class="dropdown-item" href="#">Translation Audio <span><i class="fas fa-chevron-right"></i></span></a>
											<ul class="sub-dropdown">
												<li><a class="" href="reciting-n-translation.php">Reciting & Translation</a></li>
												<li><a class="" href="fahem-ul-quran.php">Urdu Translation Only</a></li>
											</ul>										
										</li>
										<li>
											<a class="dropdown-item" href="#">Literal Translation <span><i class="fas fa-chevron-right"></i></span></a>
											<ul class="sub-dropdown">
												<li><a class="" href="literal-n-iIdiomatic-urdu.php">Literal & Idiomatic Urdu</a></li>
												<li><a class="" href="literal-n-iIdiomatic-english.php">Literal & Idiomatic Englsh</a></li>
												<li><a class="" href="literal-n-iIdiomatic-urdu-n-english.php">Literal & Idiomatic Urdu & English</a></li>
											</ul>
										</li>
										<li>
											<a class="dropdown-item" href="#">Lets Understand Quran <span><i class="fas fa-chevron-right"></i></span></a>
											<ul class="sub-dropdown">
												<li><a class="" href="reciting-n-translation.php">Reciting & Translation</a></li>
												<li><a class="" href="fahem-ul-quran.php">Urdu Translation Only</a></li>
											</ul>
										</li>
										<li>
											<a class="dropdown-item" href="quranic-grammer.php">Quranic Grammer</a>
										</li>
										<li>
											<a class="dropdown-item" href="#">Dua-E-Quran <span><i class="fas fa-chevron-right"></i></span></a>
											<ul class="sub-dropdown">
												<li><a class="" href="taraweeh.php">Today\'s Taraweeh</a></li>
												<li><a class="" href="Articles.php">Articles On The Quran</a></li>
											</ul>
										</li>
										<li>
											<a class="dropdown-item" href="tafseer-a-quran.php">Tafseer-A-Quran</a>
										</li>
									</ul>
								</li>

								<li class="nav-item">
									<a class="nav-link" href="javascript:;">For School & Colleges <span><i class="fas fa-chevron-down"></i></span></a>
									<ul class="dropdown-menu">
										<li><a class="dropdown-item" href="class-6.php">6th</a></li>
										<li><a class="dropdown-item" href="class-7.php">7th</a></li>
										<li><a class="dropdown-item" href="class-8.php">8th</a></li>
										<li><a class="dropdown-item" href="class-9.php">9th</a></li>
										<li><a class="dropdown-item" href="class-10.php">10th</a></li>
										<li><a class="dropdown-item" href="class-11.php">11th</a></li>
										<li><a class="dropdown-item" href="class-12.php">12th</a></li>
									</ul>
								</li>								
								<li class="nav-item">
									<a class="nav-link" href="javascript:;">Muslims Should Know <span><i class="fas fa-chevron-down"></i></span></a>
									<ul class="dropdown-menu">
										<li><a class="dropdown-item" href="namaz-translation.php">Namaz Translation</a></li>
										<li><a class="dropdown-item" href="Dua-A-Qanoot.php">Dua-A-Qunoot</a></li>
										<li><a class="dropdown-item" href="surah-fatiha.php">Surah Fatiha</a></li>
										<li><a class="dropdown-item" href="surah-yaseen.php">Surah Yaseen</a></li>
										<li><a class="dropdown-item" href="surah-rahman.php">Surah Rahman</a></li>
										<li><a class="dropdown-item" href="Darood-E-Taj.php">Darood-E-Taj</a></li>
									</ul>
								</li>								
								
								<li class="nav-item">
									<a class="nav-link" href="contact.php">Contact</a>
								</li>
							</ul>
						</nav>
						<ul
							class="d-xl-flex d-lg-flex d-md-none d-sm-none d-none justify-content-end align-items-center social-media-icons">

							<li>
								<a href="javascript:;">
									<span class="sidebar-toggle">
										<svg width="19" height="19" viewBox="0 0 19 19" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path
												d="M7.49833 0.000312674C3.3688 0.000312674 0 3.36934 0 7.49864C0 11.6279 3.36902 14.997 7.49833 14.997C9.2042 14.997 10.7819 14.4241 12.0444 13.4581L17.2855 18.7073C17.676 19.0978 18.3167 19.0978 18.7072 18.7073C19.0976 18.3169 19.0976 17.684 18.7072 17.2936L13.458 12.0444C14.4236 10.782 14.9968 9.20393 14.9968 7.49833C14.9968 3.3688 11.6276 0 7.49846 0L7.49833 0.000312674ZM7.49833 1.99985C10.5472 1.99985 12.9975 4.45002 12.9975 7.49904C12.9975 10.5481 10.5473 12.9982 7.49833 12.9982C4.44931 12.9982 1.99914 10.5481 1.99914 7.49904C1.99914 4.45002 4.44931 1.99985 7.49833 1.99985Z"
												fill="white" />
										</svg>
									</span>
								</a>
							</li>
							<li>
								<a class="redButton" href="Register.php"><span>Register Now</span></a>
							</li>

						</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- responsive menu bar start -->
		<div class="mobile-menu-wrapper d-xl-none d-lg-none d-md-block d-sm-block">
			<div class="container">
				<div class="row">
					<div class=" col-md-4 col-sm-4 col-4">
						<div class="mobile-logo">
						<a href="#">
							<img src="assets/images/footer-logo.png" alt="logo">
						</a>
						</div>
					</div>
					<div class="col-md-8 col-sm-8 col-8">
						<div class="d-flex  justify-content-end">
							<div class="social-media-icons">
								<ul>
									<li>
										<a href="javascript:;">
										<span class="sidebar-toggle">
											<svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
												<path
													d="M7.49833 0.000312674C3.3688 0.000312674 0 3.36934 0 7.49864C0 11.6279 3.36902 14.997 7.49833 14.997C9.2042 14.997 10.7819 14.4241 12.0444 13.4581L17.2855 18.7073C17.676 19.0978 18.3167 19.0978 18.7072 18.7073C19.0976 18.3169 19.0976 17.684 18.7072 17.2936L13.458 12.0444C14.4236 10.782 14.9968 9.20393 14.9968 7.49833C14.9968 3.3688 11.6276 0 7.49846 0L7.49833 0.000312674ZM7.49833 1.99985C10.5472 1.99985 12.9975 4.45002 12.9975 7.49904C12.9975 10.5481 10.5473 12.9982 7.49833 12.9982C4.44931 12.9982 1.99914 10.5481 1.99914 7.49904C1.99914 4.45002 4.44931 1.99985 7.49833 1.99985Z"
													fill="white" />
											</svg>

										</span>
										</a>
									</li>
								</ul>
							</div>
							<div class="d-flex">
								<div class="toggle-main-wrapper click-toggle" id="sidebar-toggle">
									<span class="line"></span>
									<span class="line"></span>
									<span class="line"></span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="sidebar">
			<div class="sidebar_logo">
				<a href="index.php"><img src="assets/images/footer-logo.png" alt="logo"></a>
			</div>

			<div id="cssmenu">
				<ul class="float_left">
					<li><a href="Home.php">Home</a></li>
					<li><a href="Aboutus.php">About Us</a></li>
					<li class="has-sub">
						<a href="javascript:;">Subjects</a>
						<ul>
							<li class="has-sub">
								<a href="#">Quran</a>
								<ul>
									<li><a class="" href="class-6.php">Translation</a></li>
									<li><a class="" href="quranic-grammer.php">Quranic Grammer</a></li>
									<li><a class="" href="Courses.php">Dua-E-Quran</a></li>
									<li><a class="" href="Courses.php">Mazameen-E-Quran</a></li>
									<li><a class="" href="Courses.php">Quran Translation For Schools</a></li>
								</ul>
							</li>
							<li class="">
								<a href="coming-soon.php">Hadees</a>
							</li>
							<li class="">
								<a href="coming-soon.php">Fiqh</a>
							</li>
							<li class="">
								<a href="coming-soon.php">Belief / Aqaid</a>
							</li>
							<li class="">
								<a href="coming-soon.php">Seerat</a>
							</li>
						</ul>
					</li>
					<li class="has-sub">
						<a href="javascript:;">Quran Study</a>
						<ul>
							<li class="has-sub">
								<a href="#">Translation Audio </a>
								<ul>
									<li><a class="" href="reciting-n-translation.php">Reciting & Translation</a></li>
									<li><a class="" href="fahem-ul-quran.php">Urdu Translation Only</a></li>
								</ul>										
							</li>
							<li class="has-sub">
								<a href="#">Literal Translation </a>
								<ul>
									<li><a class="" href="literal-n-iIdiomatic-urdu.php">Literal & Idiomatic Urdu</a></li>
									<li><a class="" href="literal-n-iIdiomatic-english.php">Literal & Idiomatic Englsh</a></li>
									<li><a class="" href="literal-n-iIdiomatic-urdu-n-english.php">Literal & Idiomatic Urdu & English</a></li>
								</ul>
							</li>
							<li class="has-sub">
								<a href="#">Lets Understand Quran </a>
								<ul>
									<li><a class="" href="reciting-n-translation.php">Reciting & Translation</a></li>
									<li><a class="" href="fahem-ul-quran.php">Urdu Translation Only</a></li>
								</ul>
							</li>
							<li class="">
								<a href="quranic-grammer.php">Quranic Grammer</a>
							</li>
							<li class="has-sub">
								<a href="#">Dua-E-Quran </a>
								<ul>
									<li><a class="" href="taraweeh.php">Today\'s Taraweeh</a></li>
									<li><a class="" href="Articles.php">Articles On The Quran</a></li>
								</ul>
							</li>
							<li class="">
								<a href="tafseer-a-quran.php">Tafseer-A-Quran</a>
							</li>
						</ul>
					</li>					
					<li class="has-sub">
						<a href="javascript:;">For School & Colleges </a>
						<ul>
							<li><a class="" href="class-6.php">6th</a></li>
							<li><a class="" href="class-7.php">7th</a></li>
							<li><a class="" href="class-8.php">8th</a></li>
							<li><a class="" href="class-9.php">9th</a></li>
							<li><a class="" href="class-10.php">10th</a></li>
							<li><a class="" href="class-11.php">11th</a></li>
							<li><a class="" href="class-12.php">12th</a></li>
						</ul>
					</li>
					<li class="has-sub">
						<a href="javascript:;">Muslims Should Know </a>
						<ul>
							<li><a class="" href="namaz-translation.php">Namaz Translation</a></li>
							<li><a class="" href="Dua-A-Qanoot.php">Dua-A-Qunoot</a></li>
							<li><a class="" href="surah-fatiha.php">Surah Fatiha</a></li>
							<li><a class="" href="surah-yaseen.php">Surah Yaseen</a></li>
							<li><a class="" href="surah-rahman.php">Surah Rahman</a></li>
							<li><a class="" href="Darood-E-Taj.php">Darood-E-Taj</a></li>
						</ul>
					</li>
					<li><a href="contact.php">Contact Us</a></li>
				</ul>
				<div class="center-btn">
					<a class="redButton" href="Register.php"><span>Register Now</span></a>
				</div>
			</div>
		</div>
		<!-- responsive menu End -->
	</div>
</div>
<!-- header end -->
';
