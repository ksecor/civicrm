// borrowed from http://www.themaninblue.com/scripts/content_height.js



function checkContentHeight()
{
	if (document.getElementById)
	{
		contentLeftHandle = document.getElementById("contentLeft");
		contentRightHandle = document.getElementById("contentRight");
	}
	else if (document.all)
	{
		contentLeftHandle = document.all["contentLeft"];
		contentRightHandle = document.all["contentRight"];
	}

	if (contentLeftHandle.scrollHeight < 200)
	{
		contentLeftHandle.style.height = "200px";
	}

	if (contentLeftHandle.scrollHeight < contentRightHandle.scrollHeight)
	{
		contentLeftHandle.style.height = contentRightHandle.scrollHeight + 50 + "px";
	}

	return;
}
