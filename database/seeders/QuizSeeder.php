<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        // Tạo tài khoản admin mẫu
        DB::table('accounts')->insert([
            'email' => 'admin@tracnghiem.com',
            'password' => Hash::make('admin123'),
            'full_name' => 'Admin Trắc Nghiệm',
            'role' => 'ADMIN',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tạo các chủ đề
        $topics = [
            ['name' => 'Tổng quan Mã nguồn mở & Giấy phép', 'slug' => 'tong-quan-ma-nguon-mo', 'description' => 'Khái niệm OSS, các loại giấy phép: GPL, MIT, BSD, Apache, LGPL, MPL', 'sort_order' => 1],
            ['name' => 'Hệ điều hành Linux', 'slug' => 'linux', 'description' => 'Kernel Linux, cấu trúc thư mục, các lệnh cơ bản, phân quyền', 'sort_order' => 2],
            ['name' => 'Shell Script', 'slug' => 'shell-script', 'description' => 'Cú pháp Bash, biến, vòng lặp, điều kiện, hàm', 'sort_order' => 3],
            ['name' => 'Git & GitHub', 'slug' => 'git-github', 'description' => 'Quản lý phiên bản, các lệnh git, workflow', 'sort_order' => 4],
            ['name' => 'Docker', 'slug' => 'docker', 'description' => 'Container, Image, Docker Compose, Docker Hub', 'sort_order' => 5],
            ['name' => 'Bugzilla', 'slug' => 'bugzilla', 'description' => 'Hệ thống theo dõi lỗi, trạng thái bug, vòng đời bug', 'sort_order' => 6],
            ['name' => 'LAMP Stack', 'slug' => 'lamp-stack', 'description' => 'Linux, Apache, MySQL, PHP - Cài đặt và cấu hình', 'sort_order' => 7],
            ['name' => 'VS Code', 'slug' => 'vscode', 'description' => 'Phím tắt, Extension, Debug, Cấu hình', 'sort_order' => 8],
            ['name' => 'CMS (WordPress, Joomla, OpenCart)', 'slug' => 'cms', 'description' => 'Hệ quản trị nội dung, Theme, Plugin', 'sort_order' => 9],
            ['name' => 'Python Web Framework', 'slug' => 'python-framework', 'description' => 'Django, Flask, MVT, ORM, CRUD', 'sort_order' => 10],
        ];

        foreach ($topics as $topic) {
            $topic['created_at'] = now();
            $topic['updated_at'] = now();
            DB::table('quiz_topics')->insert($topic);
        }

        // Tạo các đề thi
        $exams = [
            ['id' => 1, 'name' => 'Đề Ôn Thầy Trường (CK)', 'slug' => 'de-on-thay-truong', 'description' => 'Đề ôn tập cuối kỳ của thầy Trường - Bao gồm các chủ đề: Mã nguồn mở, Linux, Shell Script, Git, Docker, Bugzilla', 'type' => 'practice', 'time_limit' => 60, 'is_active' => true],
            ['id' => 2, 'name' => 'Trắc nghiệm nguồn mở (Bonus)', 'slug' => 'trac-nghiem-nguon-mo-bonus', 'description' => 'Đề trắc nghiệm bổ sung về phần mềm mã nguồn mở - Chương 1,2,3,4 và các chủ đề nâng cao', 'type' => 'practice', 'time_limit' => 90, 'is_active' => true],
            ['id' => 3, 'name' => 'Ôn tập Thầy Khuê', 'slug' => 'on-tap-thay-khue', 'description' => 'Đề ôn tập của thầy Khuê - 7 chủ đề: Tổng quan OSS, Linux Shell, VS Code, LAMP, CMS, Python Framework, Bugzilla', 'type' => 'practice', 'time_limit' => 90, 'is_active' => true],
            ['id' => 4, 'name' => 'Đề Trộn 50 Câu', 'slug' => 'de-tron-50-cau', 'description' => 'Đề thi trộn ngẫu nhiên 50 câu từ tất cả các đề để luyện tập toàn diện', 'type' => 'mixed', 'time_limit' => 45, 'total_questions' => 50, 'is_active' => true],
        ];

        foreach ($exams as $exam) {
            $exam['created_at'] = now();
            $exam['updated_at'] = now();
            DB::table('quiz_exams')->insert($exam);
        }

        // ========== ĐỀ 1: ĐỀ ÔN THẦY TRƯỜNG (CK) ==========
        $exam1Questions = [
            // Phần mềm mã nguồn mở
            ['question' => 'Phần lớn phần mềm mã nguồn mở là kết quả của', 'option_a' => 'Niềm đam mê lập trình', 'option_b' => 'Kết quả của một số bài tập trong các chương trình đại học', 'option_c' => 'Vì Lợi ích cộng đồng', 'option_d' => 'Tất cả các điều trên', 'correct_answer' => 'd', 'explanation' => 'Phần mềm mã nguồn mở được tạo ra từ nhiều nguồn động lực khác nhau: đam mê, học tập, và vì lợi ích cộng đồng.', 'topic_id' => 1],

            ['question' => 'Phần mềm văn phòng nào sau đây sử dụng giấy phép mã nguồn mở', 'option_a' => 'Microsoft Office', 'option_b' => 'Libre Office', 'option_c' => 'Adobe Acrobat', 'option_d' => 'WinRAR', 'correct_answer' => 'b', 'explanation' => 'LibreOffice là bộ phần mềm văn phòng mã nguồn mở, sử dụng giấy phép Mozilla Public License.', 'topic_id' => 1],

            ['question' => 'Phần mềm Mozilla Firefox sử dụng giấy phép mã nguồn mở nào', 'option_a' => 'Mozilla Public License', 'option_b' => 'GNU GPL', 'option_c' => 'Apache License', 'option_d' => 'MIT License', 'correct_answer' => 'a', 'explanation' => 'Mozilla Firefox được phát hành dưới giấy phép Mozilla Public License (MPL).', 'topic_id' => 1],

            ['question' => 'Phần mềm Apache Server sử dụng giấy phép nào sau đây', 'option_a' => 'BSD License', 'option_b' => 'GNU GPL', 'option_c' => 'Mozilla Public License', 'option_d' => 'Apache License', 'correct_answer' => 'd', 'explanation' => 'Apache HTTP Server sử dụng Apache License 2.0.', 'topic_id' => 1],

            ['question' => 'Những phần mềm mã nguồn mở miễn phí nào sau đây giúp chạy các ứng dụng windows trên môi trường Ubuntu', 'option_a' => 'VirtualBox và VMware', 'option_b' => 'Photoshop và AI', 'option_c' => 'Wine và PlayOnLinux', 'option_d' => 'Office và Excel', 'correct_answer' => 'c', 'explanation' => 'Wine là lớp tương thích giúp chạy ứng dụng Windows trên Linux. PlayOnLinux là frontend giúp dễ dàng cài đặt và quản lý Wine.', 'topic_id' => 1],

            ['question' => 'Giấy phép nào có khả năng kết hợp một phần mềm với một phần mềm thư viện mang giấy phép mở tương ứng', 'option_a' => 'LGPL (Lesser General Public License)', 'option_b' => 'BSD License', 'option_c' => 'Apache License', 'option_d' => 'Cả 3 License', 'correct_answer' => 'a', 'explanation' => 'LGPL cho phép liên kết (link) với thư viện mà không yêu cầu mã nguồn của chương trình chính phải mở.', 'topic_id' => 1],

            // Linux
            ['question' => 'Làm cách nào để xóa người dùng cùng với thư mục home trong Linux?', 'option_a' => 'userdel', 'option_b' => 'userdel -r', 'option_c' => 'deluser --force', 'option_d' => 'rmuser -h', 'correct_answer' => 'b', 'explanation' => 'Lệnh userdel -r sẽ xóa user và đồng thời xóa thư mục home của user đó.', 'topic_id' => 2],

            ['question' => 'Lệnh nào xem danh sách các file của thư mục gốc hệ điều hành Ubuntu?', 'option_a' => 'dir C:', 'option_b' => 'show /', 'option_c' => 'list all', 'option_d' => 'ls /', 'correct_answer' => 'd', 'explanation' => 'Trong Linux, thư mục gốc là / và lệnh ls dùng để liệt kê nội dung thư mục.', 'topic_id' => 2],

            ['question' => 'Môi trường nào không liên quan đến hệ điều hành Linux', 'option_a' => 'GNOME', 'option_b' => 'XFCE', 'option_c' => 'KDE', 'option_d' => 'Metro', 'correct_answer' => 'd', 'explanation' => 'Metro là giao diện của Windows 8. GNOME, KDE, XFCE đều là môi trường desktop cho Linux.', 'topic_id' => 2],

            ['question' => 'Để sử dụng các lệnh Linux trên windows, ta có thể cài đặt', 'option_a' => 'WSL', 'option_b' => 'WINE', 'option_c' => 'PlayOnLinux', 'option_d' => 'Bootcamp', 'correct_answer' => 'a', 'explanation' => 'WSL (Windows Subsystem for Linux) cho phép chạy môi trường Linux trực tiếp trên Windows.', 'topic_id' => 2],

            ['question' => 'Một trong những đặc điểm nổi bật của hệ thống file ext3, ext4 là gì?', 'option_a' => 'Thời gian kiểm tra hệ thống file (fsck) rất lâu', 'option_b' => 'Không hỗ trợ tính năng Journaling', 'option_c' => 'Chỉ tương thích với hệ điều hành Windows', 'option_d' => 'Khởi động nhanh', 'correct_answer' => 'd', 'explanation' => 'ext3/ext4 hỗ trợ Journaling giúp khởi động nhanh và phục hồi dữ liệu khi có sự cố.', 'topic_id' => 2],

            ['question' => 'Tác giả của phiên bản hệ điều hành Linux đầu tiên là?', 'option_a' => 'Steve Jobs', 'option_b' => 'Bill Gates', 'option_c' => 'Richard Stallman', 'option_d' => 'Linus Torvalds', 'correct_answer' => 'd', 'explanation' => 'Linus Torvalds là người tạo ra kernel Linux vào năm 1991.', 'topic_id' => 2],

            ['question' => 'Phần quan trọng nhất của hệ điều hành Linux là:', 'option_a' => 'Kernel', 'option_b' => 'Shell', 'option_c' => 'Application', 'option_d' => 'Desktop Environment', 'correct_answer' => 'a', 'explanation' => 'Kernel là nhân của hệ điều hành, quản lý tài nguyên phần cứng và cung cấp dịch vụ cho các chương trình.', 'topic_id' => 2],

            ['question' => 'Số phiên bản kernel của Linux có gì đặc biệt', 'option_a' => 'Số phiên bản luôn bắt đầu bằng số 0', 'option_b' => 'Số thứ 2 là số chẵn: Phiên bản ổn định (Stable)', 'option_c' => 'Số thứ 2 là số lẻ: Phiên bản thử nghiệm', 'option_d' => 'Câu b và c đúng', 'correct_answer' => 'd', 'explanation' => 'Trong các phiên bản kernel Linux cũ, số thứ hai chẵn nghĩa là stable, lẻ là development.', 'topic_id' => 2],

            ['question' => 'Để xem hướng dẫn cách dùng lệnh ls, ta thực hiện lệnh:', 'option_a' => 'help ls', 'option_b' => 'ls ?', 'option_c' => 'man ls', 'option_d' => 'show ls', 'correct_answer' => 'c', 'explanation' => 'man (manual) là lệnh để xem hướng dẫn sử dụng chi tiết của các lệnh trong Linux.', 'topic_id' => 2],

            // Copyleft và GPL
            ['question' => 'Quan niệm nào sau đây sai về copyleft:', 'option_a' => 'Copyleft là việc sử dụng luật bản quyền để đảm bảo quyền tự do sao chép và sửa đổi phần mềm', 'option_b' => 'Copyleft bắt buộc các phiên bản sửa đổi phải phát hành dưới cùng giấy phép tự do', 'option_c' => 'Mục đích của Copyleft là ngăn chặn việc biến phần mềm tự do thành phần mềm độc quyền', 'option_d' => 'Copyleft cho phép biến mã nguồn mở thành mã nguồn đóng (closed source)', 'correct_answer' => 'd', 'explanation' => 'Đáp án d SAI vì copyleft KHÔNG cho phép biến mã nguồn mở thành mã nguồn đóng. Ngược lại, copyleft yêu cầu mọi phiên bản phái sinh đều phải giữ nguyên giấy phép mở. Các đáp án a, b, c đều đúng về copyleft.', 'topic_id' => 1],

            ['question' => 'Nếu bạn viết 1 chương trình áp dụng giấy phép GNU-GPL thì bạn cần đính kèm những thông báo đi cùng phần mềm ở đâu:', 'option_a' => 'Chỉ cần ghi trong tài liệu hướng dẫn sử dụng', 'option_b' => 'Đính kèm vào phần đầu của tập tin mã nguồn (dưới dạng ghi chú)', 'option_c' => 'Gửi thông báo đăng ký về tổ chức FSF', 'option_d' => 'Chỉ cần hiển thị logo GPL trong phần giới thiệu', 'correct_answer' => 'b', 'explanation' => 'GPL yêu cầu đính kèm thông báo giấy phép ở đầu mỗi file source code.', 'topic_id' => 1],

            ['question' => 'Giấy phép mã nguồn mở là tập hợp các quy tắc đòi hỏi ai là người phải tuân theo', 'option_a' => 'Người sáng chế ra phần mềm mã nguồn mở', 'option_b' => 'Nhà bảo hành phần mềm mã nguồn mở', 'option_c' => 'Người sử dụng phần mềm mã nguồn mở', 'option_d' => 'Cả 3 phương án trên đều đúng', 'correct_answer' => 'd', 'explanation' => 'Giấy phép mã nguồn mở áp dụng cho tất cả các bên liên quan: tác giả, người phân phối và người sử dụng.', 'topic_id' => 1],

            ['question' => 'Phát biểu nào sau đây không phải là phát biểu đúng', 'option_a' => 'Phần mềm mã nguồn mở là phần mềm tự do', 'option_b' => 'Phần mềm mã nguồn mở cho phép người dùng quyền truy cập vào mã nguồn', 'option_c' => 'Phần mềm mã nguồn mở cho phép người dùng quyền sửa đổi và cải tiến mã nguồn', 'option_d' => 'Phần mềm mã nguồn mở cho phép phân phối lại các bản sao', 'correct_answer' => 'a', 'explanation' => 'Open Source và Free Software có định nghĩa khác nhau. OSS tập trung vào mã nguồn mở, còn Free Software tập trung vào quyền tự do của người dùng.', 'topic_id' => 1],

            ['question' => 'Câu nào sau đây không phải là ưu điểm của mã nguồn mở', 'option_a' => 'Chi phí bản quyền thấp (thường là miễn phí)', 'option_b' => 'Khả năng bảo mật cao (do có nhiều người soi code)', 'option_c' => 'Không bị phụ thuộc vào một nhà cung cấp duy nhất', 'option_d' => 'Phần mềm mã nguồn mở có độ ổn định cao', 'correct_answer' => 'd', 'explanation' => 'Độ ổn định phụ thuộc vào từng dự án cụ thể, không phải đặc điểm chung của tất cả phần mềm mã nguồn mở.', 'topic_id' => 1],

            ['question' => 'Câu nào sau đây là đúng', 'option_a' => 'Phần mềm mã nguồn mở không được phép thương mại hóa', 'option_b' => 'Phần mềm mã nguồn mở không được pháp luật bảo hộ quyền tác giả', 'option_c' => 'Tác giả phần mềm mã nguồn mở phải chịu trách nhiệm đền bù nếu phần mềm gây lỗi', 'option_d' => 'Phần mềm mã nguồn mở không có bảo hành', 'correct_answer' => 'd', 'explanation' => 'Hầu hết các giấy phép OSS đều có điều khoản "AS IS" - không có bảo hành.', 'topic_id' => 1],

            ['question' => 'Giấy phép GNU GPL phiên bản mới nhất là:', 'option_a' => '1.0', 'option_b' => '2.0', 'option_c' => '3.0', 'option_d' => '4.0', 'correct_answer' => 'c', 'explanation' => 'GPL phiên bản 3.0 được phát hành năm 2007 và là phiên bản mới nhất.', 'topic_id' => 1],

            ['question' => 'Bạn hãy cho biết phần mềm nào sau đây sử dụng giấy phép GNU GPL:', 'option_a' => 'Ubuntu', 'option_b' => 'Apache Server', 'option_c' => 'Mozilla Firefox', 'option_d' => 'Internet Explorer', 'correct_answer' => 'a', 'explanation' => 'Ubuntu (Linux) sử dụng giấy phép GPL. Apache dùng Apache License, Firefox dùng MPL.', 'topic_id' => 1],

            ['question' => 'Giấy phép nào không cấp phép một phần mềm/thư viện mã nguồn đóng liên kết với một phần mềm/thư viện mang giấy phép mở tương ứng', 'option_a' => 'GNU General Public License', 'option_b' => 'GNU Lesser General Public License', 'option_c' => 'MIT License', 'option_d' => 'BSD License', 'correct_answer' => 'a', 'explanation' => 'GPL là giấy phép "lây lan" - yêu cầu code kết hợp cũng phải GPL. LGPL, MIT, BSD cho phép kết hợp với mã nguồn đóng.', 'topic_id' => 1],

            // Desktop Environment
            ['question' => 'Môi trường đồ họa KDE là gì?', 'option_a' => 'Là môi trường màn hình nền hiện đại trên các hệ máy tính chạy Unix/Linux và cũng chạy được trên Windows và Mac OS', 'option_b' => 'Là một hệ quản trị cơ sở dữ liệu mã nguồn mở', 'option_c' => 'Là một trình biên dịch C/C++ trên môi trường Linux', 'option_d' => 'Là một trình duyệt web mặc định trên Linux', 'correct_answer' => 'a', 'explanation' => 'KDE (K Desktop Environment) là môi trường desktop đa nền tảng cho Unix/Linux, có thể chạy trên Windows và Mac thông qua Cygwin và Fink.', 'topic_id' => 2],

            ['question' => 'Thư viện nào sau đây là nền tảng thư viện của môi trường giao diện Gnome', 'option_a' => 'Qt', 'option_b' => 'GTK+', 'option_c' => 'MFC', 'option_d' => 'Cocoa', 'correct_answer' => 'b', 'explanation' => 'GNOME sử dụng thư viện GTK+ (GIMP Toolkit). KDE sử dụng Qt. MFC là của Windows, Cocoa của macOS.', 'topic_id' => 2],

            ['question' => 'Trong Ubuntu, luôn có một tài khoản mặc định:', 'option_a' => 'root', 'option_b' => 'admin', 'option_c' => 'guest', 'option_d' => 'user', 'correct_answer' => 'a', 'explanation' => 'Tài khoản root luôn tồn tại trong mọi hệ thống Linux, đó là superuser có quyền cao nhất.', 'topic_id' => 2],

            ['question' => 'Môi trường giao diện nào trong Linux phù hợp cho các loại máy chủ cần tính ổn định và tốc độ nhanh', 'option_a' => 'Môi trường giao diện văn bản (Console)', 'option_b' => 'Môi trường đồ họa GNOME', 'option_c' => 'Môi trường đồ họa KDE', 'option_d' => 'Môi trường giao diện X-Window', 'correct_answer' => 'a', 'explanation' => 'Môi trường console (command line) tiêu tốn ít tài nguyên nhất, phù hợp cho server.', 'topic_id' => 2],

            // Shell
            ['question' => 'Các shell có sẵn trong các hệ thống Linux:', 'option_a' => 'sh (Bourne Shell)', 'option_b' => 'bash (Bourne Again Shell)', 'option_c' => 'csh (C Shell)', 'option_d' => 'Tất cả đều đúng', 'correct_answer' => 'd', 'explanation' => 'Linux hỗ trợ nhiều loại shell: sh, bash, csh, zsh, ksh, fish...', 'topic_id' => 3],

            ['question' => 'Các shell script được lưu với phần mở rộng tập là:', 'option_a' => '.bat', 'option_b' => '.sh', 'option_c' => '.exe', 'option_d' => '.cmd', 'correct_answer' => 'b', 'explanation' => 'Shell script trong Linux thường có đuôi .sh (mặc dù không bắt buộc).', 'topic_id' => 3],

            ['question' => 'Tại sao cần shell script?', 'option_a' => 'Để tránh việc phải gõ đi gõ lại các lệnh', 'option_b' => 'Để tự động hóa các tác vụ quản trị hệ thống', 'option_c' => 'Để thực hiện một chuỗi các lệnh phức tạp', 'option_d' => 'Tất cả đều đúng', 'correct_answer' => 'd', 'explanation' => 'Shell script giúp tự động hóa công việc, tiết kiệm thời gian và giảm sai sót.', 'topic_id' => 3],

            ['question' => 'Đâu là comment 1 dòng trong shell script', 'option_a' => '# comment', 'option_b' => '// comment', 'option_c' => '/* comment */', 'option_d' => '**', 'correct_answer' => 'a', 'explanation' => 'Trong shell script, ký tự # được dùng để bắt đầu một comment.', 'topic_id' => 3],

            ['question' => 'Lệnh #!/bin/bash ở đầu file shell script để', 'option_a' => 'Khai báo chương trình script nào dịch và thực thi đoạn script', 'option_b' => 'Là một dòng chú thích đơn thuần', 'option_c' => 'Cấp quyền thực thi cho file script', 'option_d' => 'Khai báo đường dẫn thư mục hiện hành', 'correct_answer' => 'a', 'explanation' => 'Dòng shebang (#!) chỉ định interpreter sẽ được sử dụng để chạy script.', 'topic_id' => 3],

            // Apache và LAMP
            ['question' => 'Khi cài đặt Apache trong Ubuntu, mặc định apache được cài đặt vào thư mục nào?', 'option_a' => '/var', 'option_b' => '/home', 'option_c' => '/bin', 'option_d' => '/etc', 'correct_answer' => 'd', 'explanation' => 'File cấu hình Apache nằm trong /etc/apache2. Document root mặc định trong /var/www/html.', 'topic_id' => 7],

            ['question' => 'File cấu hình apache2 trong linux là:', 'option_a' => 'httpd.conf', 'option_b' => 'apache2.conf', 'option_c' => 'config.apache', 'option_d' => 'web.conf', 'correct_answer' => 'b', 'explanation' => 'Trên Ubuntu/Debian, file cấu hình chính là apache2.conf. Trên CentOS/RHEL là httpd.conf.', 'topic_id' => 7],

            ['question' => 'Trong Linux,', 'option_a' => 'LAMP là viết tắt của Linux, Apache, Mysql, Php', 'option_b' => 'LAPP là Linux, Apache, PostgreSQL, PHP', 'option_c' => 'LLMP là Linux, Lighttpd, MySQL/MariaDB, PHP/Perl/Python', 'option_d' => 'Tất cả các câu trên đều đúng', 'correct_answer' => 'd', 'explanation' => 'Có nhiều stack phát triển web phổ biến: LAMP, LAPP, LEMP (Nginx), LLMP (Lighttpd).', 'topic_id' => 7],

            // Git
            ['question' => 'Git là', 'option_a' => 'Phần mềm quản lý phiên bản phân tán', 'option_b' => 'Phần mềm quản lý phiên bản tập trung', 'option_c' => 'Một ngôn ngữ lập trình', 'option_d' => 'Một hệ điều hành mã nguồn mở', 'correct_answer' => 'a', 'explanation' => 'Git là Distributed Version Control System (DVCS) - hệ thống quản lý phiên bản phân tán.', 'topic_id' => 4],

            ['question' => 'Bugzilla là hệ thống phần mềm theo dõi lỗi mã nguồn mở. Nó là một website chạy trên:', 'option_a' => 'Apache, Mysql, PHP', 'option_b' => 'IIS, SQL Server, ASP.NET', 'option_c' => 'Apache, Mysql, Perl', 'option_d' => 'Nginx, PostgreSQL, Python', 'correct_answer' => 'c', 'explanation' => 'Bugzilla được viết bằng Perl và thường chạy trên Apache với MySQL.', 'topic_id' => 6],

            ['question' => 'Trong Bugzilla, đâu là trạng thái của một bug?', 'option_a' => 'NEW', 'option_b' => 'ASSIGNED', 'option_c' => 'CLOSED', 'option_d' => 'Tất cả đều đúng', 'correct_answer' => 'd', 'explanation' => 'Bugzilla có nhiều trạng thái bug: NEW, ASSIGNED, RESOLVED, VERIFIED, CLOSED, REOPENED...', 'topic_id' => 6],

            ['question' => 'Đối tượng sử dụng và quản lý Bugzilla thường là:', 'option_a' => 'Lập trình viên (Developer)', 'option_b' => 'Tester/Quality Control (QC)', 'option_c' => 'Quản trị mạng (Network Administrator)', 'option_d' => 'Người dùng cuối (End User)', 'correct_answer' => 'b', 'explanation' => 'Bugzilla chủ yếu được sử dụng bởi team phát triển và QA để quản lý bugs.', 'topic_id' => 6],

            // Versioning
            ['question' => 'Định dạng phiên bản phần mềm theo quy tắc:', 'option_a' => 'YEAR.MONTH.DAY', 'option_b' => 'ALPHA.BETA.RC', 'option_c' => 'RELEASE.UPDATE.FIX', 'option_d' => 'MAJOR.MINOR.PATCH', 'correct_answer' => 'd', 'explanation' => 'Semantic Versioning (SemVer) sử dụng định dạng MAJOR.MINOR.PATCH (ví dụ: 1.2.3).', 'topic_id' => 1],

            ['question' => 'Đâu là phiên bản tiền phát hành (phiên bản có thể không ổn định)?', 'option_a' => '1.0.0-alpha', 'option_b' => '1.0.0', 'option_c' => '1.0.1', 'option_d' => '2.0.0', 'correct_answer' => 'a', 'explanation' => 'Phiên bản alpha, beta, RC (Release Candidate) là các phiên bản tiền phát hành.', 'topic_id' => 1],

            ['question' => 'Phiên bản phần mềm Beta là:', 'option_a' => 'Phiên bản dùng thử, nhằm tung ra để người dùng public rộng rãi sử dụng, phản hồi', 'option_b' => 'Phiên bản kiểm thử nội bộ (Alpha)', 'option_c' => 'Phiên bản chính thức (Stable Release)', 'option_d' => 'Phiên bản giới hạn (Demo/Trial)', 'correct_answer' => 'a', 'explanation' => 'Beta là phiên bản gần hoàn thiện, được phát hành rộng rãi để thu thập phản hồi từ người dùng.', 'topic_id' => 1],

            // Git commands
            ['question' => 'Lệnh git status', 'option_a' => 'Kiểm tra trạng thái của những file đã thay đổi trong local repository', 'option_b' => 'Xem lịch sử các lần commit trước đó', 'option_c' => 'Hiển thị chi tiết nội dung code khác biệt', 'option_d' => 'Đẩy các tập tin đã thay đổi lên Remote Repository', 'correct_answer' => 'a', 'explanation' => 'git status hiển thị trạng thái working directory: file nào thay đổi, file nào đã staged.', 'topic_id' => 4],

            ['question' => 'Lệnh git add', 'option_a' => 'Tạo một kho chứa Git mới tại thư mục hiện hành', 'option_b' => 'Ghi lại các thay đổi vào lịch sử phiên bản', 'option_c' => 'Tải xuống toàn bộ mã nguồn từ kho chứa từ xa', 'option_d' => 'Đánh chỉ mục (index) các nội dung mới, chuẩn bị cho lần commit tiếp theo', 'correct_answer' => 'd', 'explanation' => 'git add thêm file vào staging area, chuẩn bị cho commit.', 'topic_id' => 4],

            ['question' => 'Trong git, để chuyển sang nhánh branch1, sử dụng lệnh nào?', 'option_a' => 'git branch branch1', 'option_b' => 'git merge branch1', 'option_c' => 'git add branch1', 'option_d' => 'git checkout branch1', 'correct_answer' => 'd', 'explanation' => 'git checkout dùng để chuyển branch. Cũng có thể dùng git switch (Git 2.23+).', 'topic_id' => 4],

            ['question' => 'Câu nào không đúng trong git?', 'option_a' => 'Lệnh pull: sao chép các thay đổi từ local repository sang remote repository', 'option_b' => 'Lệnh push: sao chép các thay đổi từ local repository sang remote repository', 'option_c' => 'Lệnh fetch: tải các thay đổi từ remote về nhưng chưa merge', 'option_d' => 'Lệnh commit: lưu lại các thay đổi vào local repository', 'correct_answer' => 'a', 'explanation' => 'git pull tải thay đổi từ remote về local (ngược lại với mô tả). git push mới đẩy từ local lên remote.', 'topic_id' => 4],

            ['question' => 'Trong git, lệnh clone:', 'option_a' => 'Khởi tạo một kho chứa mới và rỗng', 'option_b' => 'Tạo một bản sao repository trên máy local', 'option_c' => 'Cập nhật các thay đổi mới nhất từ server về máy', 'option_d' => 'Đưa các file từ thư mục làm việc vào vùng chờ', 'correct_answer' => 'b', 'explanation' => 'git clone tạo bản sao hoàn chỉnh của repository từ remote về local.', 'topic_id' => 4],

            // Linux filesystem
            ['question' => 'Đâu là một hệ điều hành nhân Linux?', 'option_a' => 'Debian', 'option_b' => 'Linux Mint', 'option_c' => 'Cả 2 đều đúng', 'option_d' => 'Cả 2 đều sai', 'correct_answer' => 'c', 'explanation' => 'Debian và Linux Mint đều là các bản phân phối (distro) sử dụng nhân Linux.', 'topic_id' => 2],

            ['question' => 'Trong hệ thống tập tin Linux, tập tin thiết bị ngoại vi được chứa trong:', 'option_a' => '/dev', 'option_b' => '/bin', 'option_c' => '/etc', 'option_d' => '/home', 'correct_answer' => 'a', 'explanation' => '/dev chứa các device files (tập tin đại diện cho thiết bị phần cứng).', 'topic_id' => 2],

            ['question' => 'Lệnh in đường dẫn của thư mục hiện hành:', 'option_a' => 'pwd', 'option_b' => 'ls', 'option_c' => 'cd', 'option_d' => 'path', 'correct_answer' => 'a', 'explanation' => 'pwd (Print Working Directory) in ra đường dẫn thư mục hiện tại.', 'topic_id' => 2],

            ['question' => 'Lệnh chuyển thư mục về thư mục cha là', 'option_a' => 'cd /', 'option_b' => 'cd ..', 'option_c' => 'changedir /', 'option_d' => 'changedir ..', 'correct_answer' => 'b', 'explanation' => 'cd .. chuyển lên thư mục cha. cd / chuyển về thư mục gốc.', 'topic_id' => 2],

            ['question' => 'Lệnh xóa thư mục data/dir1 không cần hỏi', 'option_a' => 'delete -f data/dir1', 'option_b' => 'rm -rf data/dir1', 'option_c' => 'remove data/dir1', 'option_d' => 'Tất cả đều đúng', 'correct_answer' => 'b', 'explanation' => 'rm -rf: -r (recursive) xóa đệ quy, -f (force) không hỏi xác nhận.', 'topic_id' => 2],

            ['question' => 'Để copy file a.txt và lưu thành file b.txt, sử dụng lệnh', 'option_a' => 'cp b.txt a.txt', 'option_b' => 'copy a.txt b.txt', 'option_c' => 'copy b.txt a.txt', 'option_d' => 'cp a.txt b.txt', 'correct_answer' => 'd', 'explanation' => 'cp [nguồn] [đích] - sao chép file nguồn thành file đích.', 'topic_id' => 2],

            ['question' => 'Lệnh mv a.txt c.txt', 'option_a' => 'Xoá các file a.txt và c.txt', 'option_b' => 'Đổi tên a.txt thành c.txt', 'option_c' => 'Đổi tên c.txt thành a.txt', 'option_d' => 'Tất cả đều sai', 'correct_answer' => 'b', 'explanation' => 'mv (move) có thể dùng để di chuyển hoặc đổi tên file.', 'topic_id' => 2],

            ['question' => 'Lệnh thiết lập cho tất cả các user có thể read, write và execute file a.txt là', 'option_a' => 'chmod a.txt 777', 'option_b' => 'chmod 777 a.txt', 'option_c' => 'set 777 a.txt', 'option_d' => 'Tất cả đúng', 'correct_answer' => 'b', 'explanation' => 'chmod 777 file: 7=rwx cho owner, group và others.', 'topic_id' => 2],

            ['question' => 'Câu lệnh nào thay đổi quyền truy cập file thành rw-r--r-x?', 'option_a' => 'chmod 546 file', 'option_b' => 'chmod 642 file', 'option_c' => 'chmod 645 file', 'option_d' => 'chmod 655 file', 'correct_answer' => 'c', 'explanation' => 'rw-=6, r--=4, r-x=5. Vậy chmod 645 tạo quyền rw-r--r-x.', 'topic_id' => 2],

            ['question' => 'Tên các tập tin và thư mục trong linux', 'option_a' => 'Phân biệt hoa, thường', 'option_b' => 'Không phân biệt hoa, thường', 'option_c' => 'Chỉ được phép sử dụng chữ thường', 'option_d' => 'Bắt buộc phải có phần mở rộng', 'correct_answer' => 'a', 'explanation' => 'Linux phân biệt chữ hoa/thường (case-sensitive). file.txt và FILE.txt là 2 file khác nhau.', 'topic_id' => 2],
        ];

        $order = 1;
        foreach ($exam1Questions as $q) {
            DB::table('quiz_questions')->insert([
                'exam_id' => 1,
                'topic_id' => $q['topic_id'],
                'question' => $q['question'],
                'option_a' => $q['option_a'],
                'option_b' => $q['option_b'],
                'option_c' => $q['option_c'] ?? null,
                'option_d' => $q['option_d'] ?? null,
                'correct_answer' => $q['correct_answer'],
                'explanation' => $q['explanation'],
                'sort_order' => $order++,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Cập nhật total_questions
        DB::table('quiz_exams')->where('id', 1)->update(['total_questions' => count($exam1Questions)]);

        $this->command->info('Đã seed ' . count($exam1Questions) . ' câu hỏi cho Đề 1');

        // ========== THÊM LÝ THUYẾT ==========
        $this->seedTheories();
    }

    private function seedTheories()
    {
        $theories = [
            [
                'topic_id' => 1,
                'title' => 'Các loại giấy phép mã nguồn mở',
                'slug' => 'cac-loai-giay-phep-ma-nguon-mo',
                'content' => '
# Các loại Giấy phép Mã nguồn mở

## 1. GNU General Public License (GPL)
- **Đặc điểm**: Giấy phép Copyleft mạnh nhất
- **Yêu cầu**:
  - Mã nguồn phái sinh phải được mở dưới GPL
  - Phải cung cấp mã nguồn khi phân phối
- **Phần mềm sử dụng**: Linux kernel, WordPress, MySQL (Community)

## 2. MIT License
- **Đặc điểm**: Rất linh hoạt, ít ràng buộc
- **Cho phép**:
  - Sử dụng, sao chép, sửa đổi, phân phối
  - Có thể đóng mã nguồn
- **Phần mềm sử dụng**: jQuery, React, Node.js

## 3. Apache License 2.0
- **Đặc điểm**:
  - Tương tự MIT nhưng có điều khoản về bằng sáng chế
  - Bảo vệ người đóng góp khỏi kiện tụng patent
- **Phần mềm sử dụng**: Apache HTTP Server, Android

## 4. BSD License
- **Đặc điểm**: Rất tự do, giống MIT
- **Có 2 phiên bản**: 2-clause và 3-clause
- **Phần mềm sử dụng**: FreeBSD, Nginx

## 5. LGPL (Lesser GPL)
- **Đặc điểm**:
  - Cho phép liên kết với phần mềm đóng
  - Chỉ yêu cầu mở phần thư viện LGPL
- **Phần mềm sử dụng**: GTK+, glibc

## 6. Mozilla Public License (MPL)
- **Đặc điểm**:
  - File-level copyleft
  - Chỉ file sửa đổi cần mở
- **Phần mềm sử dụng**: Firefox, LibreOffice
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 2,
                'title' => 'Cấu trúc thư mục Linux',
                'slug' => 'cau-truc-thu-muc-linux',
                'content' => '
# Cấu trúc Thư mục Linux

## Thư mục gốc /

| Thư mục | Mô tả |
|---------|-------|
| `/bin` | Binary - Chứa các lệnh cơ bản (ls, cp, mv) |
| `/boot` | Chứa kernel và bootloader |
| `/dev` | Device files - Đại diện cho thiết bị phần cứng |
| `/etc` | Etcetera - File cấu hình hệ thống |
| `/home` | Thư mục home của người dùng |
| `/lib` | Thư viện chia sẻ (shared libraries) |
| `/mnt` | Mount point cho filesystem tạm thời |
| `/opt` | Optional - Phần mềm bổ sung |
| `/proc` | Process - Thông tin về process và kernel |
| `/root` | Thư mục home của root user |
| `/sbin` | System binaries - Lệnh quản trị hệ thống |
| `/tmp` | Temporary - File tạm thời |
| `/usr` | Unix System Resources - Chương trình người dùng |
| `/var` | Variable - Dữ liệu thay đổi (logs, cache) |

## Các lệnh cơ bản

```bash
pwd     # In thư mục hiện tại
ls      # Liệt kê file/thư mục
cd      # Chuyển thư mục
mkdir   # Tạo thư mục
rm      # Xóa file/thư mục
cp      # Sao chép
mv      # Di chuyển/đổi tên
cat     # Xem nội dung file
chmod   # Thay đổi quyền
chown   # Thay đổi chủ sở hữu
```

## Phân quyền trong Linux

| Ký hiệu | Số | Quyền |
|---------|-----|-------|
| r | 4 | Read - Đọc |
| w | 2 | Write - Ghi |
| x | 1 | Execute - Thực thi |

**Ví dụ**: `chmod 755 file`
- Owner: 7 = rwx (4+2+1)
- Group: 5 = r-x (4+1)
- Others: 5 = r-x (4+1)
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 3,
                'title' => 'Shell Script cơ bản',
                'slug' => 'shell-script-co-ban',
                'content' => '
# Shell Script Cơ bản

## 1. Cấu trúc file script

```bash
#!/bin/bash
# Dòng shebang - chỉ định interpreter

# Đây là comment
echo "Hello World"
```

## 2. Biến

```bash
# Khai báo biến (không có khoảng trắng quanh =)
name="Linux"
age=30

# Sử dụng biến
echo $name
echo ${name}
echo "Tôi đang học $name"

# Biến hệ thống
echo $HOME
echo $USER
echo $PWD
```

## 3. Đọc input

```bash
read -p "Nhập tên: " name
echo "Xin chào $name"
```

## 4. Điều kiện

```bash
if [ $a -eq $b ]; then
    echo "a bằng b"
elif [ $a -gt $b ]; then
    echo "a lớn hơn b"
else
    echo "a nhỏ hơn b"
fi
```

**Toán tử so sánh số:**
- `-eq`: bằng (equal)
- `-ne`: không bằng (not equal)
- `-gt`: lớn hơn (greater than)
- `-lt`: nhỏ hơn (less than)
- `-ge`: lớn hơn hoặc bằng
- `-le`: nhỏ hơn hoặc bằng

## 5. Vòng lặp

```bash
# For loop
for i in 1 2 3 4 5; do
    echo $i
done

# For với range
for i in {1..5}; do
    echo $i
done

# While loop
count=1
while [ $count -le 5 ]; do
    echo $count
    ((count++))
done
```

## 6. Mảng

```bash
arr=("apple" "banana" "cherry")

echo ${arr[0]}      # Phần tử đầu tiên
echo ${arr[@]}      # Tất cả phần tử
echo ${#arr[@]}     # Số phần tử
```

## 7. Hàm

```bash
function greet() {
    echo "Hello $1"
}

greet "World"
```
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 4,
                'title' => 'Git - Quản lý phiên bản',
                'slug' => 'git-quan-ly-phien-ban',
                'content' => '
# Git - Hệ thống quản lý phiên bản

## Khái niệm cơ bản

- **Repository (Repo)**: Kho chứa mã nguồn
- **Commit**: Snapshot của code tại một thời điểm
- **Branch**: Nhánh phát triển độc lập
- **Merge**: Gộp các nhánh
- **Remote**: Kho chứa từ xa (GitHub, GitLab)

## Các lệnh Git cơ bản

### Thiết lập ban đầu
```bash
git config --global user.name "Tên của bạn"
git config --global user.email "email@example.com"
```

### Khởi tạo và Clone
```bash
git init                # Khởi tạo repo mới
git clone <url>         # Clone repo từ remote
```

### Làm việc với file
```bash
git status              # Xem trạng thái
git add <file>          # Thêm file vào staging
git add .               # Thêm tất cả file
git commit -m "message" # Commit với message
```

### Làm việc với remote
```bash
git remote add origin <url>   # Thêm remote
git push origin <branch>      # Đẩy lên remote
git pull origin <branch>      # Kéo từ remote
git fetch                     # Tải về nhưng không merge
```

### Làm việc với branch
```bash
git branch              # Liệt kê branch
git branch <name>       # Tạo branch mới
git checkout <branch>   # Chuyển branch
git checkout -b <name>  # Tạo và chuyển branch
git merge <branch>      # Gộp branch
git branch -d <branch>  # Xóa branch
```

### Xem lịch sử
```bash
git log                 # Xem lịch sử commit
git log --oneline       # Xem ngắn gọn
git diff                # Xem thay đổi
```

## Git Workflow

1. **Clone** hoặc **Pull** code mới nhất
2. Tạo **Branch** cho tính năng mới
3. **Add** và **Commit** thay đổi
4. **Push** lên remote
5. Tạo **Pull Request**
6. **Review** và **Merge**
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 5,
                'title' => 'Docker cơ bản',
                'slug' => 'docker-co-ban',
                'content' => '
# Docker Cơ bản

## Khái niệm

- **Image**: Template chỉ đọc để tạo container
- **Container**: Instance đang chạy của image
- **Dockerfile**: File định nghĩa cách build image
- **Docker Hub**: Registry công cộng chứa images

## Các lệnh Docker cơ bản

### Làm việc với Image
```bash
docker images                    # Liệt kê images
docker pull <image>              # Tải image từ Hub
docker rmi <image>               # Xóa image
docker build -t <name> .         # Build image từ Dockerfile
docker push <image>              # Đẩy image lên Hub
```

### Làm việc với Container
```bash
docker ps                        # Liệt kê container đang chạy
docker ps -a                     # Liệt kê tất cả container
docker run <image>               # Tạo và chạy container
docker run -d <image>            # Chạy ở chế độ nền (detached)
docker run -it <image> /bin/bash # Chạy với terminal tương tác
docker start <container>         # Khởi động container
docker stop <container>          # Dừng container
docker rm <container>            # Xóa container
docker exec -it <container> bash # Truy cập vào container
```

### Dockerfile cơ bản
```dockerfile
FROM ubuntu:20.04
RUN apt-get update && apt-get install -y nginx
COPY . /var/www/html
EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
```

### Docker Compose
```yaml
version: "3"
services:
  web:
    image: nginx
    ports:
      - "80:80"
  db:
    image: mysql
    environment:
      MYSQL_ROOT_PASSWORD: password
```

```bash
docker-compose up -d             # Chạy services
docker-compose down              # Dừng services
docker-compose logs              # Xem logs
```
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 6,
                'title' => 'Bugzilla - Hệ thống theo dõi lỗi',
                'slug' => 'bugzilla-he-thong-theo-doi-loi',
                'content' => '
# Bugzilla - Hệ thống theo dõi lỗi

## Giới thiệu
Bugzilla là phần mềm mã nguồn mở dùng để theo dõi và quản lý bugs trong quá trình phát triển phần mềm. Được phát triển bởi Mozilla Foundation và viết bằng Perl.

## Các trạng thái Bug

| Trạng thái | Mô tả |
|------------|-------|
| **NEW/UNCONFIRMED** | Bug mới được báo cáo |
| **ASSIGNED** | Đã giao cho developer xử lý |
| **RESOLVED** | Đã giải quyết |
| **VERIFIED** | QA đã xác nhận sửa đúng |
| **CLOSED** | Đóng hoàn toàn |
| **REOPENED** | Mở lại vì vẫn còn lỗi |

## Resolution (Kết quả giải quyết)

| Resolution | Ý nghĩa |
|------------|---------|
| **FIXED** | Đã sửa xong |
| **INVALID** | Không phải bug, do user hiểu sai |
| **WONTFIX** | Không sửa (do design hoặc không cần thiết) |
| **DUPLICATE** | Trùng với bug khác |
| **WORKSFORME** | Không tái hiện được lỗi |

## Các trường quan trọng

- **Product**: Sản phẩm
- **Component**: Module/tính năng
- **Version**: Phiên bản phần mềm
- **Severity**: Mức độ nghiêm trọng (Blocker, Critical, Major, Minor, Trivial)
- **Priority**: Độ ưu tiên (P1 - P5)
- **Assignee**: Người được giao xử lý
- **CC List**: Danh sách người theo dõi

## Vòng đời Bug

```
UNCONFIRMED → NEW → ASSIGNED → RESOLVED → VERIFIED → CLOSED
                         ↓                    ↓
                    REOPENED ←←←←←←←←←←←←←←←←←
```
',
                'sort_order' => 1
            ],
            [
                'topic_id' => 7,
                'title' => 'LAMP Stack',
                'slug' => 'lamp-stack',
                'content' => '
# LAMP Stack

## LAMP là gì?
LAMP là bộ công cụ phát triển web mã nguồn mở phổ biến:
- **L**inux - Hệ điều hành
- **A**pache - Web server
- **M**ySQL/MariaDB - Cơ sở dữ liệu
- **P**HP/Python/Perl - Ngôn ngữ lập trình

## Cài đặt trên Ubuntu

```bash
# Cập nhật package
sudo apt update

# Cài Apache
sudo apt install apache2

# Cài MySQL
sudo apt install mysql-server
sudo mysql_secure_installation

# Cài PHP
sudo apt install php libapache2-mod-php php-mysql

# Khởi động lại Apache
sudo systemctl restart apache2
```

## Cấu hình Apache

**File cấu hình chính**: `/etc/apache2/apache2.conf`
**Document Root**: `/var/www/html`
**Virtual Hosts**: `/etc/apache2/sites-available/`

```bash
# Bật module rewrite
sudo a2enmod rewrite

# Bật site
sudo a2ensite mysite.conf

# Tắt site
sudo a2dissite 000-default.conf

# Restart Apache
sudo systemctl restart apache2
```

## MySQL cơ bản

```bash
# Đăng nhập MySQL
sudo mysql -u root -p

# Tạo database
CREATE DATABASE mydb;

# Tạo user
CREATE USER \'user\'@\'localhost\' IDENTIFIED BY \'password\';

# Cấp quyền
GRANT ALL PRIVILEGES ON mydb.* TO \'user\'@\'localhost\';
FLUSH PRIVILEGES;
```

## PHP Info

Tạo file `/var/www/html/info.php`:
```php
<?php
phpinfo();
?>
```

**Các port mặc định:**
- HTTP: 80
- HTTPS: 443
- MySQL: 3306
',
                'sort_order' => 1
            ],
        ];

        foreach ($theories as $theory) {
            $theory['created_at'] = now();
            $theory['updated_at'] = now();
            DB::table('quiz_theories')->insert($theory);
        }

        $this->command->info('Đã seed ' . count($theories) . ' bài lý thuyết');
    }
}
