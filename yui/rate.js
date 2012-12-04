
YUI.add('moodle-block_course_ratings-rate', function(Y) {
	M.block_course_ratings = {
	    init : function() {
	       var rating1 = new Y.StarRating({
		        boundingBox: '#rating1',
		        defaultSelected: 3,
		        disabled: false,
		        label: 'Label...',
		        on: {
		            itemOver: function() {
		                Y.log('itemOver 1', arguments);
		            },
		            itemOut: function() {
		                Y.log('itemOut 1', arguments);
		            },
		            itemClick: function(event) {
		                Y.log('itemClick 1', arguments);
		            },
		            itemSelect: function(event) {
		                var instance = event.target;
		 
		                Y.log('itemSelect 1', arguments);
		            }
		        }
    		}).render();/*/
    var ratings = new Y.Ratings({ srcNode: "#myWidget_basic" });
    ratings.render();*/
		}
	}
},
'@VERSION@', {
	    requires:['gallery-aui-rating', 'moodle-block_course_ratings-load']
}
);