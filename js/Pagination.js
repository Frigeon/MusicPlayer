function Paginate(list, perPage, page, index)
{
	var NumPagesMod = list.length % perPage;
	var NumPages = parseInt(list.length / perPage);
	
	if(NumPagesMod > 0)
	{
		NumPages += 1;
	}
	
	// Number of page links in the begin and end of whole range
	var count_out = 5;
	// Number of page links on each side of current page
	var count_in = 3;
	
	if(count_in < count_out && (page < (count_out - (count_in - 1)) || page > (NumPages - (count_out - (count_in - 1)))))
	{
		count_in = count_out;
	}
	
	// Beginning group of pages: $n1...$n2
	var n1 = 1;
	var n2 = Math.min(NumPages, count_out);
	
	// Ending group of pages: $n7...$n8
	var n7 = Math.max(1, (NumPages - count_out + 1));
	var n8 = NumPages;
	
	// Middle group of pages: $n4...$n5
	var n4 = Math.max((n2 + 1), (page - count_in));
	var n5 = Math.min((n7 - 1), (page + count_in));
	var use_middle = (n5 >= n4);
	
	// Point $n3 between $n2 and $n4
	var n3 = parseInt((n2 + n4) / 2);
	var use_n3 = (use_middle && ((n4 - n2) > 1));
	
	// Point $n6 between $n5 and $n7
	var n6 = parseInt((n5 + n7) / 2);
	var use_n6 = (use_middle && ((n7 - n5) > 1));
	
	//console.log(n1 + ' | ' + n2 + ' | ' + n3 + ' | ' + n4 + ' | ' + n5 + ' | ' + n6 + ' | ' + n7 + ' | ' + n8);
	
	// Links to display as array(page => content)
	var links = Array();
	
	for(var i = n1; i <= n2; i++)
	{
		links[i] = i;
	}
	
	if(use_n3)
	{
		links[n3] = '&hellip;';
	}
	
	for(var i = n4; i <= n5; i++)
	{
		links[i] = i;
	}
	
	if(use_n6)
	{
		links[n6] = '&hellip;';
	}
	
	for(var i = n7; i <= n8; i++)
	{
		links[i] = i;
	}
	
	var linksHTML = $(document.createElement('div')).addClass('pagination');
	linksHTML.append($(document.createElement('ul')).addClass('pagination'));
	
	var first = $(document.createElement('li')).append($(document.createElement('span')).addClass('FakeLink').css({'font-size':'12px'}).addClass('FirstPage').attr('onclick', 'Paginate(tracks, 30, 1, index)').text('First Page'));
	var last = $(document.createElement('li')).append($(document.createElement('span')).addClass('FakeLink').css({'font-size':'12px'}).addClass('LastPage').attr('onclick', 'Paginate(tracks, 30, '+NumPages+', index)').text('Last Page'));
	var prev = $(document.createElement('li')).append($(document.createElement('span')).addClass('FakeLink').css({'font-size':'12px'}).addClass('PreviousPage').attr('onclick', 'Paginate(tracks, 30, '+(page-1 > 0 ? page-1 : NumPages)+', index)').text('|<< Page'));
	var next = $(document.createElement('li')).append($(document.createElement('span')).addClass('FakeLink').css({'font-size':'12px'}).addClass('NextPage').attr('onclick', 'Paginate(tracks, 30, '+((page+1) < (NumPages+1) ? page+1 : 1)+', index)').text('Page >>|'));
	
	var paginationObject = linksHTML.find('ul.pagination');
	paginationObject.append(first);
	paginationObject.append(prev);
	
	//console.log(links);
	
	for(key in links)
	{
		paginationObject.append('<li' + (key == page ? ' class="active"' : '') + '><span class="FakeLink" style="font-size:12px;" onclick="Paginate(tracks, ' + perPage + ', ' + key + ', index)">'+links[key]+'</span></li>');
	};
	
	paginationObject.append(next);
	paginationObject.append(last);
	
	var htmlArr = Array();
	var pageArr = Array();
	var pageBott = Array();
	
/*	for(var p = 0; p < NumPages; p++)
	{
		var pageLink = $(document.createElement('a')).attr('id', p).text('-'+(p+1)+'-|');
		var pageBottomLink = $(document.createElement('a')).attr('id', p+'b').text('-'+(p+1)+'-|');
		
		if(p+1 == NumPages)
		{
			var lnText = pageLink.text();
			pageLink.text(lnText.replace('|', ''));
		}
		
		if(p == page){
			pageLink.addClass('selected');
			pageBottomLink.addClass('selected');
		}
		
		pageArr.push(pageLink);
		pageBott.push(pageBottomLink);
	}*/

	page = page-1;
	for(var i = (page * perPage); i < (page * perPage) + perPage; i++)
	{
		if(list.hasOwnProperty(i)){
			var plNum = $(document.createElement('div')).addClass('plNum').attr('id', i).text(i < 9 ? '0'+(i+1) : i+1);
			var plTitle = $(document.createElement('div')).addClass('plTitle').text(list[i].name);
			var plItem = $(document.createElement('div')).addClass('plItem').html(plNum).append(plTitle);
			var listItem = $(document.createElement('li')).html(plItem);
			
			if(i == index)
			{
				listItem.addClass('plSel');
			}
			
			htmlArr.push(listItem);
		}
	}
	
	$('#plUL').html('');
	for(var x = 0; x < htmlArr.length; x++)
	{
		$('#plUL').append(htmlArr[x]);
	}
	
	$('#spanWrapTop').html(paginationObject);
	$('#spanWrapBottom').html(paginationObject.clone(true));
	
	$('#plUL li').on('click', function() {
			var id = parseInt($(this).find('.plNum').attr('id'));
			if(id != index) {
				playTrack(id);
			}
		});
}