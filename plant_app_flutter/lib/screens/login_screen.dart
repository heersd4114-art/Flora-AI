import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../providers/auth_provider.dart';
import 'home_screen.dart'; 
import 'register_screen.dart'; 
import 'forgot_password_screen.dart';
import 'admin_dashboard.dart';
import 'partner_dashboard.dart';

class LoginScreen extends StatefulWidget {
  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  bool _isPasswordVisible = false;

  void _login() async {
    if (_emailController.text.isEmpty || _passwordController.text.isEmpty) {
      ScaffoldMessenger.of(context).showSnackBar(const SnackBar(content: Text('Please fill all fields')));
      return;
    }

    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    
    try {
      final success = await authProvider.login(
        _emailController.text.trim(), 
        _passwordController.text,
      );

      if (!mounted) return;

      if (success && authProvider.user != null) {
        final role = authProvider.user!.role.toLowerCase();
        if (role == 'admin') {
          Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => AdminDashboard()));
        } else if (role == 'partner' || role == 'delivery_partner') {
          Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => PartnerDashboard()));
        } else {
          Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => HomeScreen()));
        }
      } else {
        showDialog(
          context: context,
          builder: (ctx) => AlertDialog(
            title: const Text('Login Failed'),
            content: const Text('Invalid credentials or user not found.'),
            actions: [TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK'))],
          )
        );
      }
    } catch (e) {
      print("Login Error: $e");
      if (!mounted) return;
      showDialog(
        context: context,
        builder: (ctx) => AlertDialog(
          title: const Text('Connection Error'),
          content: Text('Could not connect to server.\n\nDetails: $e\n\nCheck your IP address in config.dart.'),
          actions: [TextButton(onPressed: () => Navigator.pop(ctx), child: const Text('OK'))],
        )
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      body: Padding(
        padding: EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Icon(Icons.local_florist, size: 48, color: Colors.green[800]),
                SizedBox(width: 12),
                Text('Flora Ai', style: GoogleFonts.poppins(fontSize: 36, fontWeight: FontWeight.bold, color: Colors.green[800])),
              ],
            ),
            SizedBox(height: 8),
            Center(child: Text('Welcome Back!', style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.w600, color: Colors.black87))),
            SizedBox(height: 48),
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                labelText: 'Email', 
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                prefixIcon: Icon(Icons.email_outlined)
              ),
            ),
            SizedBox(height: 16),
            TextField(
              controller: _passwordController,
              obscureText: !_isPasswordVisible,
              decoration: InputDecoration(
                labelText: 'Password',
                border: OutlineInputBorder(borderRadius: BorderRadius.circular(12)),
                prefixIcon: Icon(Icons.lock_outline),
                suffixIcon: IconButton(
                  icon: Icon(_isPasswordVisible ? Icons.visibility : Icons.visibility_off),
                  onPressed: () => setState(() => _isPasswordVisible = !_isPasswordVisible),
                )
              ),
            ),
            const SizedBox(height: 8),
            Align(
              alignment: Alignment.centerRight,
              child: TextButton(
                onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => ForgotPasswordScreen())),
                child: Text('Forgot Password?', style: GoogleFonts.poppins(color: Colors.green[800])),
              ),
            ),
            SizedBox(height: 16),
            ElevatedButton(
              onPressed: context.watch<AuthProvider>().isLoading ? null : _login,
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green[600],
                padding: EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
              ),
              child: context.watch<AuthProvider>().isLoading ? const CircularProgressIndicator(color: Colors.white) : Text('Login', style: GoogleFonts.poppins(fontSize: 18, color: Colors.white)),
            ),
            SizedBox(height: 16),
            TextButton(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => RegisterScreen())),
              child: Text("Don't have an account? Sign Up", style: GoogleFonts.poppins(color: Colors.green[800])),
            )
          ],
        ),
      ),
    );
  }
}
