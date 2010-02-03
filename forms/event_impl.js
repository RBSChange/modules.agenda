var punitval = null;

function onInit()
{
	var unitHandler =
	{
		form: this,
		handleEvent: function(event)
		{
			this.form.dispatchUnitChange(event.target);
		}
	}
	var valueHandler =
	{
		form: this,
		handleEvent: function(event)
		{
			this.form.dispatchValueChange(event.target);
		}
	}
	for (var i = 1; i < 11 ; i++)
	{
		this.fields['priority'].addItem(i, i, null, null);
	}
	this.getElementById('_homepagespanunit').addEventListener('change', unitHandler, false);
	this.getElementById('_homepagespanvalue').addEventListener('change', valueHandler, false);
	this.getElementById('_homepagespanvalue').setAttribute('disabled', true);
	this.fields['homepagespan'].hide();
}

function onLoad()
{
	var unit = null;
	var val = null;
	if( this.fields['homepagespan'].value > 0)
	{
		val = this.fields['homepagespan'].value;
		if ((val>>8) >0)
		{
			unit = 'years';
			val >>= 8;
		}
		else if ((val>>4) > 0)
		{
			unit = 'months';
			val >>=4 ;
		}
		else
		{
			unit = 'weeks';
		}
	}
	this.getElementById("_homepagespanunit").value = unit;
	this.getElementById("_homepagespanvalue").value = val;
	this.punitval = unit;
	if (val==null)
	{
		this.getElementById("_homepagespanvalue").disable();
	}
}

function dispatchValueChange(valuefield)
{

	var field = this.getElementById('_homepagespanunit');
	if (field.value == 'weeks')
	{
		this.fields['homepagespan'].value = valuefield.value;
	}
	else if (field.value == 'months')
	{
		this.fields['homepagespan'].value  = valuefield.value<<4;
	}
	else if (field.value == 'years')
	{
		this.fields['homepagespan'].value   = valuefield.value<<8;
	}
	else
	{
		this.fields['homepagespan'].value  = null;
	}

}

function dispatchUnitChange(field)
{
	var valuefield = this.getElementById('_homepagespanvalue');
	if (field.value == 'weeks')
	{
		valuefield.removeAllItems();
		for (var i = 1; i < 5 ; i++)
		{
			valuefield.addItem(i, i, null, null);
		}
		valuefield.value = 1;
		valuefield.enable();
	}
	else if (field.value == 'months')
	{
		valuefield.removeAllItems();
		for (var i = 1; i < 12 ; i++)
		{
			valuefield.addItem(i, i, null, null);
		}
		valuefield.value = 1;
		valuefield.enable();
	}
	else if (field.value == 'years')
	{
		valuefield.removeAllItems();
		for (var i = 1; i < 6 ; i++)
		{
			valuefield.addItem(i, i, null, null);
		}
		valuefield.value = 1;
		valuefield.enable();
	}
	else
	{
		valuefield.removeAllItems();
		valuefield.disable();
		valuefield.value = null;
	}
	this.punitval = field.value;

}