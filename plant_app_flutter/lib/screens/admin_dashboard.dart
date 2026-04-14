import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import 'login_screen.dart';
import 'add_product_screen.dart';
import 'users_screen.dart';
import 'delivery_management_screen.dart';
import 'admin_products_screen.dart';
import 'admin_orders_screen.dart';
import 'admin_profile_screen.dart';

class AdminDashboard extends StatefulWidget {
  @override
  _AdminDashboardState createState() => _AdminDashboardState();
}

class _AdminDashboardState extends State<AdminDashboard> {
  bool _isLoading = true;
  Map<String, dynamic> _stats = {
    'total_users': 0,
    'total_orders': 0,
    'total_revenue': 0.0,
  };

  @override
  void initState() {
    super.initState();
    _loadStats();
  }

  Future<void> _loadStats() async {
    try {
      // Mapping to admin_dashboard.php
      final res = await ApiService.post('admin_dashboard_api.php', {'action': 'get_stats'});
      if (res['status'] == 'success') {
        setState(() {
          _stats = res['data'] ?? _stats;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Admin Dashboard', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, color: Colors.white)),
        backgroundColor: Colors.blue[900],
        iconTheme: const IconThemeData(color: Colors.white),
        actions: [
          IconButton(
            icon: const Icon(Icons.person),
            onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => AdminProfileScreen())),
          ),
          IconButton(
            icon: const Icon(Icons.logout),
            onPressed: () async {
              await Provider.of<AuthProvider>(context, listen: false).logout();
              if (mounted) {
                Navigator.pushReplacement(context, MaterialPageRoute(builder: (_) => LoginScreen()));
              }
            },
          )
        ],
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : ListView(
            padding: const EdgeInsets.all(24),
            children: [
              Row(
                children: [
                   Icon(Icons.admin_panel_settings, size: 40, color: Colors.blue[900]),
                   const SizedBox(width: 16),
                   Text('System Overview', style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold)),
                ],
              ),
              const SizedBox(height: 24),
              Row(
                children: [
                  Expanded(child: _buildStatCard('Users', _stats['total_users'].toString(), Icons.people, Colors.purple)),
                  const SizedBox(width: 16),
                  Expanded(child: _buildStatCard('Orders', _stats['total_orders'].toString(), Icons.shopping_bag, Colors.orange)),
                ],
              ),
              const SizedBox(height: 16),
              _buildStatCard('Total Revenue', '₹${_stats['total_revenue']}', Icons.attach_money, Colors.green),
              const SizedBox(height: 40),
              Text('Quick Actions', style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold)),
              const SizedBox(height: 16),
              _buildAdminOption(context, 'Manage Catalog', Icons.inventory, () => Navigator.push(context, MaterialPageRoute(builder: (_) => AdminProductsScreen()))),
              _buildAdminOption(context, 'View Orders', Icons.shopping_bag, () => Navigator.push(context, MaterialPageRoute(builder: (_) => AdminOrdersScreen()))),
              _buildAdminOption(context, 'Manage Users', Icons.supervised_user_circle, () => Navigator.push(context, MaterialPageRoute(builder: (_) => UsersScreen()))),
              _buildAdminOption(context, 'Delivery Partners', Icons.local_shipping, () => Navigator.push(context, MaterialPageRoute(builder: (_) => DeliveryManagementScreen()))),
            ],
          ),
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, MaterialColor color) {
    return Card(
      elevation: 4,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Icon(icon, size: 40, color: color[700]),
            const SizedBox(height: 12),
            Text(value, style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold)),
            Text(title, style: GoogleFonts.poppins(color: Colors.grey[600])),
          ],
        ),
      ),
    );
  }

  Widget _buildAdminOption(BuildContext context, String title, IconData icon, VoidCallback onTap) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: ElevatedButton.icon(
        onPressed: onTap,
        icon: Icon(icon),
        label: Text(title, style: GoogleFonts.poppins(fontSize: 16)),
        style: ElevatedButton.styleFrom(
          backgroundColor: Colors.blue[800],
          foregroundColor: Colors.white,
          minimumSize: const Size(double.infinity, 54),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
          alignment: Alignment.centerLeft,
          padding: const EdgeInsets.symmetric(horizontal: 24)
        ),
      ),
    );
  }
}
