$(document).ready(function(){function q(){var a=[{Level:c.Level,Name:c.Name},{Level:p.Level,Name:p.Name}];switch(k){case b.Level:a.push({Name:b.Name});break;case d.Level:a.push({Name:d.Name})}a[a.length-1].IsLast=!0;return a}function t(){var a=[{Level:l.Level,Name:l.Name}];switch(k){case e.Level:a.push({Name:e.Name,IsLast:!0});break;case f.Level:a.push({Name:f.Name,IsLast:!0});break;case g.Level:a.push({Name:g.Name,IsLast:!0});break;case h.Level:a.push({Name:h.Name,IsLast:!0});break;case m.Level:a.push({Name:m.Name,
IsLast:!0})}return a}var c={Level:1,Name:"\u0418\u043d\u0434\u0438\u0432\u0438\u0434\u0443\u0430\u043b\u044c\u043d\u044b\u0435"},p={Level:2,Name:function(){return r}},b={Level:21,Name:"\u0415\u0436\u0435\u0434\u043d\u0435\u0432\u043d\u044b\u0439 \u043e\u0442\u0447\u0435\u0442"},d={Level:22,Name:"\u041e\u0442\u0447\u0435\u0442 \u043f\u043e \u0437\u0430\u0440\u043f\u043b\u0430\u0442\u0435"},l={Level:3,Name:"\u041e\u0431\u0449\u0438\u0435"},e={Level:31,Name:"\u0421\u0432\u043e\u0434\u043d\u0430\u044f \u0437\u0430\u0440\u043f\u043b\u0430\u0442\u043d\u0430\u044f \u0442\u0430\u0431\u043b\u0438\u0446\u0430"},
h={Level:32,Name:"\u041e\u0431\u0449\u0430\u044f \u0437/\u043f \u0442\u0430\u0431\u043b\u0438\u0446\u0430"},g={Level:33,Name:"\u041e\u0431\u0449\u0430\u044f \u0442\u0430\u0431\u043b\u0438\u0446\u0430 \u043f\u043e \u0441\u043e\u0442\u0440\u0443\u0434\u043d\u0438\u043a\u0430\u043c"},f={Level:34,Name:"\u0421\u0432\u043e\u0434\u043d\u0430\u044f \u0442\u0430\u0431\u043b\u0438\u0446\u0430 \u0440\u0430\u0441\u043f\u0440\u0435\u0434\u0435\u043b\u0435\u043d\u0438\u044f"},m={Level:35,Name:"\u0421\u0442\u0430\u0442\u0438\u0441\u0442\u0438\u043a\u0430 \u043f\u043e \u043a\u043b\u0438\u0435\u043d\u0442\u043a\u0430\u043c"},
k=0,r="",n=0,u={data:[{Level:c.Level,Name:c.Name.toUpperCase()},{Level:l.Level,Name:l.Name.toUpperCase()}]},v={bread:[{Name:l.Name,IsLast:!0}],data:[{Level:g.Level,Name:g.Name,IsDoc:!0},{Level:e.Level,Name:e.Name,IsDoc:!0},{Level:h.Level,Name:h.Name,IsDoc:!0},{Level:f.Level,Name:f.Name,IsDoc:!0},{Level:m.Level,Name:m.Name,IsDoc:!0}]},w={bread:q,data:[{Level:b.Level,Name:b.Name,IsDoc:!0},{Level:d.Level,Name:d.Name,IsDoc:!0}]};$.ReportListDirector={Init:function(){this.InitActions();this.InitTemplate();
this.InitDynamicData()},InitActions:function(){$(document).on("click",".report-folder>a, .report-bread",function(a){a=$(a.target).closest("[level]");k=parseInt(a.attr("level"));if(k==p.Level){var c=a.find(".folder-name").html();c&&(r=c);(a=a.attr("id-employee"))&&(n=a)}$.ReportListDirector.ReloadReportList()})},InitDynamicData:function(){this.ReloadReportList()},InitTemplate:function(){$("#reportsTemplate").template("reportsTemplate")},ReloadReportList:function(){function a(a){$("#reports").empty();
$.tmpl("reportsTemplate",a).appendTo("#reports")}function n(b){b.status?b.records&&a({bread:[{Level:c.Level,Name:c.Name,IsLast:!0}],data:b.records}):showErrorAlert(b.message)}$("#reports").html("\u0417\u0430\u0433\u0440\u0443\u0437\u043a\u0430 \u0434\u0430\u043d\u043d\u044b\u0445...");switch(k){case 0:a(u);break;case c.Level:$.post(BaseUrl+"reports/data",{},n,"json");break;case l.Level:a(v);break;case p.Level:a(w);break;case b.Level:case d.Level:a({bread:q()});break;case e.Level:case f.Level:case g.Level:case h.Level:case m.Level:a({bread:t()});
break;default:alert("\u041e\u0448\u0438\u0431\u043a\u0430 \u0437\u0430\u0433\u0440\u0443\u0437\u043a\u0438 \u0434\u0430\u043d\u043d\u044b\u0445")}$.ReportListDirector.ShowReport(k)},ShowReport:function(a){$("#ReportIndividualDaily").toggle(a==b.Level);$("#ReportIndividualSalary").toggle(a==d.Level);$("#ReportOverallSalary").toggle(a==e.Level);$("#ReportOverallAllocation").toggle(a==f.Level);$("#ReportGeneralOfCustomers").toggle(a==g.Level);$("#ReportGeneralSalary").toggle(a==h.Level);$("#ReportGeneralCustomersStats").toggle(a==
m.Level);switch(a){case b.Level:$.ReportTranslate.setEmployee(n);$.ReportTranslate.ReloadReportMountMeta();break;case d.Level:$.ReportTranslate.setEmployee(n);$.ReportTranslate.ReloadReportSalary();break;case e.Level:$("#overlaySalarySite").find("input:first").click();break;case f.Level:$("#overallAllocationSite").find("input:first").click();break;case g.Level:$("#generalSite").find("input:first").click();break;case h.Level:$.ReportDirector.ReloadReportGeneralSalary()}}};$.ReportListDirector.Init()});
