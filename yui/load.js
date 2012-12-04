
YUI.add('moodle-block_course_ratings-load', function(Y) {
	M.block_course_ratings_load = {
	    init : function() {
		alert("ada");
        	var loader = new Y.Loader({});
			loader.addModule({
			    name: 'gallery-aui-rating',
			    requires: 'gallery-aui-base',
			    fullpath: 'http://ankit.moodle.local/stable/master/moodle/blocks/course_ratings/gallery-aui-rating/gallery-aui-rating.js',
			    type: 'js',
			});
			loader.require('widget', 'dd-drag', 'substitute', 'event-mouseenter', 'transition', 'intl', 'base', 'gallery-aui-rating');
			//loader.require('gallery-aui-base', 'gallery-aui-rating');
		    loader.onSuccess = function(o) {
		         // 
		    	alert('o.data: '+Y.dump(loader.resolve(true)));
		         alert('o.data: '+Y.dump(o.data));
		         console.log(loader.resolve(true));
		         console.log(o);
		         //
	        };
	        loader.onFailure = function(o) {
		         // 
		         alert('Error: '+Y.dump(o));
		         console.log(o);
		         //
	        };
	        loader.insert();
	        loader.resolve(true);
	    }
	}
},
'@VERSION@', {
	    requires:['loader', 'gallery-aui-rating']
}
);

/*
YUI.add('moodle-block_course_ratings-load', function(Y) {
	M.block_course_ratings_load = {
	    init : function() {
		alert("ada");
        	var loader = new Y.Loader({
			    filter: 'debug',
			    base: 'http://ankit.moodle.local/stable/master/moodle/lib/yuilib/3.7.3/build/',
			    combine: true,
			    require: ['base','event', 'node', 'dd', 'console']
			});
			loader.addModule({
			    name: 'gallery-ratings',
			    fullpath: 'http://ankit.moodle.local/stable/master/moodle/blocks/course_ratings/ratings/gallery-ratings.js',
			    type: 'js',
			    require: ['base'],
			});
			loader.require('base', 'gallery-ratings');
		    loader.onSuccess = function(o) {
		         // 
		    	alert('o.data: '+Y.dump(loader.resolve(true)));
		         alert('o.data: '+Y.dump(o.data));
		         console.log(loader.resolve(true));
		         //
	        };
	        loader.onFailure = function(o) {
		         // 
		         alert('Error: '+Y.dump(o));
		         //
	        };
	        loader.insert();
	        loader.resolve(true);
	    }
	}
},
'@VERSION@', {
	    requires:['base', 'loader', 'gallery-ratings']
}
);*/