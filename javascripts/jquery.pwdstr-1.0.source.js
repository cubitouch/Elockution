(function($){ 
     $.fn.extend({  
         pwdstr: function(el) {			
			return this.each(function() {
					
					
					
					$(this).keyup(function(){
						$(el).html(getTime($(this).val()));
					});
					
					function getTime(str){
					
					var chars = 0;
					var rate = 2800000000;
					
					if((/[a-z]/).test(str)) chars +=  26;
					if((/[A-Z]/).test(str)) chars +=  26;
					if((/[0-9]/).test(str)) chars +=  10;
					if((/[^a-zA-Z0-9]/).test(str)) chars +=  32;

					var pos = Math.pow(chars,str.length);
					var s = pos/rate;
					
					var decimalYears = s/(3600*24*365);
					var years = Math.floor(decimalYears);
					
					var decimalMonths =(decimalYears-years)*12;
					var months = Math.floor(decimalMonths);
					
					var decimalDays = (decimalMonths-months)*30;
					var days = Math.floor(decimalDays);
					
					var decimalHours = (decimalDays-days)*24;
					var hours = Math.floor(decimalHours);
					
					var decimalMinutes = (decimalHours-hours)*60;
					var minutes = Math.floor(decimalMinutes);
					
					var decimalSeconds = (decimalMinutes-minutes)*60;
					var seconds = Math.floor(decimalSeconds);
					
					var time = [];
					
					if(years > 0){
						if(years == 1)
							time.push("1 année, ");
						else
							time.push(years + " années, ");
					}
					if(months > 0){
						if(months == 1)
							time.push("1 mois, ");
						else
							time.push(months + " mois, ");
					}
					if(days > 0){
						if(days == 1)
							time.push("1 jour, ");
				 		else
							time.push(days + " jours, ");
					}
					if(hours > 0){
						if(hours == 1)
							time.push("1 heure, ");
						else
							time.push(hours + " heures, ");
					}
					if(minutes > 0){
						if(minutes == 1)
							time.push("1 minute, ");
						else
							time.push(minutes + " minutes, ");
					}
					if(seconds > 0){
						if(seconds == 1)
							time.push("1 seconde, ");
						else
							time.push(seconds + " secondes, ");
					}
					
					if(time.length <= 0)
						time = "moins d'une seconde, ";
					else if(time.length == 1)
						time = time[0];
					else
						time = time[0] + time[1];

					 return 'Bruteforce : ' + time.substring(0,time.length-2);
					}
					
			 });
        } 
    }); 
})(jQuery); 
