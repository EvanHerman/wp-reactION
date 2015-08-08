var $ = jQuery;
var Post = React.createClass({
	displayName: "Post",
	render: function() {
		/*
		*	Hoemepage post rendering
		*/
		if (! this.props.single) {
			return React.createElement("article", {
					className: 'homepage-article-container'
				}, 
				React.createElement("div", {
					className: 'featured-post-image-parent'
				}, 
				React.createElement("img", {
					src: append_preloaders( this.props.featured_image ),// populate with a pre-loader
					className: 'featured-post-image image-'+this.props.featured_image
				})), 
				append_featured_images( this.props.featured_image ),
				React.createElement("h3", null, this.props.title), 
				React.createElement("span", {
					className: 'post-meta'
				}, 'Published: ' +  moment(this.props.date).format( 'MMMM Do, YYYY' ) ),
				// console.log(   this.props.date ),
				React.createElement("div", {
					dangerouslySetInnerHTML: {
						__html: this.props.excerpt.rendered
					}
				}), 
				React.createElement("a", {
					href: ajaxInfo.site_url + '/#/post/' + this.props.id,
					className: 'btn btn-success'
				}, 'View Post'),
				// testing data for single blog post list
				// console.log( this.props ),
				React.createElement("button", {
					onClick: this.editPost,
					"data-id": this.props.id,
					className: "edit_post btn btn-primary"
				}, 
			"edit post" ) );
		} else {
			return React.createElement("article", null, React.createElement("h3", null, this.props.title), React.createElement("div", {
				dangerouslySetInnerHTML: {
					__html: this.props.content.rendered
				}
			}), React.createElement("button", {
				onClick: this.editPost,
				"data-id": this.props.id,
				className: "edit_post btn btn-primary"
			}, "edit post"));

		}
	}
});

var APP = React.createClass({
	displayName: "APP",
	render: function() {
		if (this.props && this.props.data.length) {
			var posts = this.props.data.map(function(post) {
				post.single = false;
				return React.createElement(Post, post);
			});
			return React.DOM.div(null, posts);
		} else if (this.props && this.props.data.id) {
			this.props.data.single = true;
			return React.createElement(Post, this.props.data);
		}
	}
});

/*
*	Ajax Functionality to get featured image URL
*	@ parameters 
		- attachment_id - Pass in the attachment ID and return the URL
		- attachment_size - Pass in the size you want returned
*/
function append_preloaders( attachment_id ) {
	return ajax_data.preloader_gif;
}

function append_featured_images( attachment_id ) {
	$( '.featured-post-image' ).wrapAll( '<div class="featured-post-image-wrap"></div>' );
	if( attachment_id > 0 ) {
		$.ajax({
			type: 'GET',
			url: ajaxInfo.site_url +'/wp-json/wp/v2/media/'+attachment_id,
			dataType: 'json',
			success: function(data) {
				$( '.image-'+attachment_id ).removeAttr('src').attr( 'src', data.media_details.sizes.medium.source_url );
				setTimeout( function() {	
					$( '.image-'+attachment_id ).parents( '.homepage-article-container' ).addClass( 'animated fadeInLeft' );
				}, 150);
			},
			error: function(error){
				console.log( error );
			}
		});
	}
}