import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';

import '../providers/auth_provider.dart';
import 'login_screen.dart';
import 'profile_screen.dart';
import 'order_history_screen.dart';
import 'scan_screen.dart';
import 'ai_status_screen.dart';
import 'disease_history_screen.dart';

class DashboardSidebar extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    final user = context.watch<AuthProvider>().user;

    return Drawer(
      child: Column(
        children: [
          UserAccountsDrawerHeader(
            decoration: BoxDecoration(color: Colors.green[800]),
            accountName: Text(user?.name ?? 'Guest User', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
            accountEmail: Text(user?.email ?? '', style: GoogleFonts.poppins()),
            currentAccountPicture: CircleAvatar(
              backgroundColor: Colors.white,
              child: Icon(Icons.person, size: 40, color: Colors.green[800]),
            ),
          ),
          ListTile(
            leading: const Icon(Icons.home),
            title: Text('Dashboard', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context); // Close Drawer
            },
          ),
          ListTile(
            leading: const Icon(Icons.shopping_basket),
            title: Text('My Orders', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (_) => OrderHistoryScreen()));
            },
          ),
          ListTile(
            leading: const Icon(Icons.camera_alt),
            title: Text('Flora Scanner', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (_) => ScanScreen()));
            },
          ),
          ListTile(
            leading: const Icon(Icons.history),
            title: Text('Care History', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (_) => DiseaseHistoryScreen()));
            },
          ),
          ListTile(
            leading: const Icon(Icons.cloud_sync),
            title: Text('AI Status', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (_) => AiStatusScreen()));
            },
          ),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.person),
            title: Text('Profile', style: GoogleFonts.poppins()),
            onTap: () {
              Navigator.pop(context);
              Navigator.push(context, MaterialPageRoute(builder: (_) => ProfileScreen()));
            },
          ),
          const Spacer(),
          const Divider(),
          ListTile(
            leading: const Icon(Icons.logout, color: Colors.redAccent),
            title: Text('Logout', style: GoogleFonts.poppins(color: Colors.redAccent)),
            onTap: () async {
              Navigator.pop(context);
              await context.read<AuthProvider>().logout();
              Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
            },
          ),
          const SizedBox(height: 16),
        ],
      ),
    );
  }
}
