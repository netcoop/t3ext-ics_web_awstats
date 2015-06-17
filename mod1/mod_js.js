//Funktion um die Statistik auszudrucken
function iFramePrint()
{
	/*window.focus();
	window.print();*/
	
	var fenster = window.open('', "Popupfenster", "width=400,height=300,resizable=yes");

	var content = document.getElementsByTagName('html')[0].innerHTML
	
	fenster.document.writeln(content);
	fenster.document.close();
	
	fenster.focus();
	fenster.print();
	fenster.close();
	
	return false;


}
