function getRootURL() 
{ var baseURL = location.href; 
	var rootURL = baseURL.substring(0, baseURL.indexOf('/', 7)); 
	// if the root url is localhost, don't add the directory as cassani doesn't use it 
	if (baseURL.indexOf('localhost') == -1) 
	{ return rootURL + "/"; } 
	else 
	{ return rootURL + baseURL.substring(baseURL.indexOf('/', 8), baseURL.indexOf('/', baseURL.indexOf('/', 8)+1)) + "/"; } 
}

var GB_ROOT_DIR = getRootURL()+"plugins/content/mgthumbnails/greybox/";
var tb_pathToImage = getRootURL()+"plugins/content/mgthumbnails/thickbox/loadingAnimation.gif";
