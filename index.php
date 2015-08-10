<?php get_header(); ?>
		
	<div class="content-wrap-container">
		
		<!-- Menu -->
		<button class="menu-button" id="open-button">Open Menu</button>
		
		<div class="menu-wrap">
			<nav class="menu">
				<div class="icon-list">
					<div ui-view="main-nav"></div>
					<p class="eh-nav-disclaimer"><em><small><?php printf( __( 'Reaction built with %s by <a href="%s" title="Evan Herman">Evan Herman</a>', 'wp-reaction' ), '<i class="fa fa-heart"></i>', esc_url( 'https://www.evan-herman.com' ) ); ?></em></small></p>
				</div>
			</nav>
			<button class="close-button" id="close-button">Close Menu</button>
			<div class="morph-shape" id="morph-shape" data-morph-open="M-1,0h101c0,0,0-1,0,395c0,404,0,405,0,405H-1V0z">
				<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 100 800" preserveAspectRatio="none">
					<path d="M-1,0h101c0,0-97.833,153.603-97.833,396.167C2.167,627.579,100,800,100,800H-1V0z"/>
				</svg>
			</div>
		</div>
		
		<div class="content-wrap">
			<div class="container content">
			
				<div class="row">
					<div class="col-sm-12">
						
						<div id="test-comment-box"></div>
						
						<h1>
							<a ui-sref="list"><?php echo bloginfo( 'name' ); ?></a>
						</h1>
								
						<!-- UI VIEW -->
						<div ui-view="content-view"></div>
									
						<!-- EDIT MODAL -->
						<div class="modal fade" id="editPost" tabindex="-1" role="dialog" aria-labelledby="editPost" aria-hidden="true" ng-controller="editPostCtrl">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title text-center" id="myModalLabel">Edit Post</h4>
									</div>
									<div class="modal-body">
										<form ng-submit="savePost()">
											<div class="form-group">
												<input ng-model="editPost.title.rendered" class="form-control" />
											</div>
											<div class="form-group">
												<textarea ng-model="editPost.content.rendered" class="form-control" rows="10"></textarea>
											</div>
											<button type="submit" class="btn btn-default">Save Post</button>
										</form>
									</div>				  
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		
	</div>

<?php get_footer(); ?>