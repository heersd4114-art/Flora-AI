import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import 'login_screen.dart';

class ProfileScreen extends StatefulWidget {
  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  final _nameController = TextEditingController();
  final _emailController = TextEditingController();
  
  final _currentPassController = TextEditingController();
  final _newPassController = TextEditingController();
  
  bool _isLoading = true;
  String _userId = '';

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadProfile();
    });
  }

  Future<void> _loadProfile() async {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final userId = auth.user?.id;
    if (userId == null || userId.isEmpty) {
      setState(() => _isLoading = false);
      return;
    }
    _userId = userId;
    
    try {
      final res = await ApiService.post('profile.php', {'action': 'get', 'user_id': _userId});
      if (res['status'] == 'success') {
        setState(() {
          _nameController.text = res['data']['name'] ?? '';
          _emailController.text = res['data']['email'] ?? '';
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _updateProfile() async {
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.post('profile.php', {
        'action': 'update', 
        'user_id': _userId,
        'name': _nameController.text.trim(),
        'email': _emailController.text.trim()
      });
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Update failed: $e')));
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _changePassword() async {
     setState(() => _isLoading = true);
    try {
      final res = await ApiService.post('profile.php', {
        'action': 'change_password',
        'user_id': _userId,
        'current_password': _currentPassController.text,
        'new_password': _newPassController.text
      });
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
      if (res['status'] == 'success') {
         _currentPassController.clear();
         _newPassController.clear();
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Password change failed: $e')));
    } finally {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _logout() async {
    await Provider.of<AuthProvider>(context, listen: false).logout();
    if (mounted) {
      Navigator.pushAndRemoveUntil(context, MaterialPageRoute(builder: (_) => LoginScreen()), (route) => false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Row(
          children: [
            Icon(Icons.local_florist, color: Colors.white),
            SizedBox(width: 8),
            Text('Flora Ai Profile', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
          ],
        ),
        backgroundColor: Colors.green[700],
        actions: [
          IconButton(icon: Icon(Icons.logout), onPressed: _logout)
        ],
      ),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator())
        : SingleChildScrollView(
            padding: EdgeInsets.all(24),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                CircleAvatar(
                  radius: 50,
                  backgroundColor: Colors.green[200],
                  child: Icon(Icons.person, size: 50, color: Colors.green[800]),
                ),
                SizedBox(height: 32),
                Text('Personal Information', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                SizedBox(height: 16),
                TextField(
                  controller: _nameController,
                  decoration: InputDecoration(labelText: 'Full Name', border: OutlineInputBorder()),
                ),
                SizedBox(height: 16),
                TextField(
                  controller: _emailController,
                  decoration: InputDecoration(labelText: 'Email', border: OutlineInputBorder()),
                ),
                SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _updateProfile,
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.green[700]),
                  child: Text('Save Changes', style: TextStyle(color: Colors.white)),
                ),
                Divider(height: 48),
                Text('Security', style: GoogleFonts.poppins(fontSize: 18, fontWeight: FontWeight.bold)),
                SizedBox(height: 16),
                TextField(
                  controller: _currentPassController,
                  obscureText: true,
                  decoration: InputDecoration(labelText: 'Current Password', border: OutlineInputBorder()),
                ),
                SizedBox(height: 16),
                TextField(
                  controller: _newPassController,
                  obscureText: true,
                  decoration: InputDecoration(labelText: 'New Password', border: OutlineInputBorder()),
                ),
                SizedBox(height: 16),
                ElevatedButton(
                  onPressed: _changePassword,
                  style: ElevatedButton.styleFrom(backgroundColor: Colors.orange[700]),
                  child: Text('Change Password', style: TextStyle(color: Colors.white)),
                ),
              ],
            ),
          ),
    );
  }
}
