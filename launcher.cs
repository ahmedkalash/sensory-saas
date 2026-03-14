using System;
using System.Diagnostics;
using System.IO;
using System.Threading;
using System.Windows.Forms;

namespace WebLauncher
{
    class Program
    {
        [STAThread]
        static void Main(string[] args)
        {
            try
            {
                // 1. Force kill existing php processes to free port
                KillPhp();

                string baseDir = AppDomain.CurrentDomain.BaseDirectory;
                
                // 2. Start PHP Background Server 
                Process phpProcess = new Process();
                phpProcess.StartInfo.FileName = Path.Combine(baseDir, @"xamp-php\php.exe");
                phpProcess.StartInfo.Arguments = "-S 127.0.0.1:8282 -t public server.php";
                phpProcess.StartInfo.WorkingDirectory = baseDir; 
                phpProcess.StartInfo.UseShellExecute = false;
                phpProcess.StartInfo.CreateNoWindow = true;
                phpProcess.StartInfo.WindowStyle = ProcessWindowStyle.Hidden;
                
                phpProcess.Start();

                // 3. Wait exactly 4000ms
                Thread.Sleep(4000);

                // 4. Open Microsoft Edge in App profile
                Process edgeProcess = new Process();
                edgeProcess.StartInfo.FileName = "msedge.exe";
                string localAppData = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
                string userDataDir = Path.Combine(localAppData, @"SensoryAssessment\EdgeUI");
                edgeProcess.StartInfo.Arguments = string.Format("--app=\"http://127.0.0.1:8282\" --user-data-dir=\"{0}\"", userDataDir);
                edgeProcess.StartInfo.UseShellExecute = true; // Required to automatically find msedge.exe from Windows App Paths
                edgeProcess.StartInfo.WindowStyle = ProcessWindowStyle.Normal;
                
                edgeProcess.Start();
                edgeProcess.WaitForExit(); // Halts script until Edge windows are closed
                
                // 5. User closed Edge, kill PHP background
                KillPhp();
            }
            catch (Exception ex)
            {
                MessageBox.Show("Fatal Error launching application:\n\n" + ex.Message + "\n\nStack:\n" + ex.StackTrace, "Launcher Error", MessageBoxButtons.OK, MessageBoxIcon.Error);
            }
        }

        static void KillPhp()
        {
            try
            {
                foreach (var process in Process.GetProcessesByName("php"))
                {
                    process.Kill();
                }
            }
            catch {}
        }
    }
}
