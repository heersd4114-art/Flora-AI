import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';

class AdminOrdersScreen extends StatefulWidget {
  @override
  _AdminOrdersScreenState createState() => _AdminOrdersScreenState();
}

class _AdminOrdersScreenState extends State<AdminOrdersScreen> {
  bool _isLoading = true;
  List<dynamic> _orders = [];

  @override
  void initState() {
    super.initState();
    _loadOrders();
  }

  Future<void> _loadOrders() async {
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.get('admin_orders.php'); 
      if (res['status'] == 'success' || res.containsKey('data')) {
        setState(() {
          _orders = res['data'] ?? res;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _updateStatus(dynamic orderId, String newStatus) async {
    try {
      final res = await ApiService.post('update_order_status.php', {
        'order_id': orderId,
        'status': newStatus
      });
      if (res['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Order $orderId updated to $newStatus')));
        _loadOrders();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed: ${res['message']}')));
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error updating status')));
    }
  }

  void _showStatusDialog(dynamic order) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Update Order #${order['order_id']}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(title: const Text('Pending'), onTap: () { Navigator.pop(ctx); _updateStatus(order['order_id'], 'Pending'); }),
            ListTile(title: const Text('Processing'), onTap: () { Navigator.pop(ctx); _updateStatus(order['order_id'], 'Processing'); }),
            ListTile(title: const Text('Out for Delivery'), onTap: () { Navigator.pop(ctx); _updateStatus(order['order_id'], 'Out for Delivery'); }),
            ListTile(title: const Text('Delivered'), onTap: () { Navigator.pop(ctx); _updateStatus(order['order_id'], 'Delivered'); }),
            ListTile(title: const Text('Cancelled', style: TextStyle(color: Colors.red)), onTap: () { Navigator.pop(ctx); _updateStatus(order['order_id'], 'Cancelled'); }),
          ],
        ),
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Manage Orders', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.blue[900],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : ListView.builder(
            padding: const EdgeInsets.all(16),
            itemCount: _orders.length,
            itemBuilder: (context, index) {
              final order = _orders[index];
              return Card(
                elevation: 2,
                margin: const EdgeInsets.only(bottom: 16),
                child: ListTile(
                  contentPadding: const EdgeInsets.all(16),
                  title: Text('Order #${order['order_id']} - ₹${order['total_amount']}', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                  subtitle: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      const SizedBox(height: 8),
                      Text('User ID: ${order['user_id']}', style: GoogleFonts.poppins()),
                      Text('Date: ${order['created_at']}', style: GoogleFonts.poppins(color: Colors.grey[600])),
                      const SizedBox(height: 8),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                        decoration: BoxDecoration(
                          color: _getStatusColor(order['status']).withOpacity(0.2),
                          borderRadius: BorderRadius.circular(8)
                        ),
                        child: Text(
                          order['status'] ?? 'Unknown', 
                          style: GoogleFonts.poppins(color: _getStatusColor(order['status']), fontWeight: FontWeight.bold, fontSize: 12)
                        )
                      )
                    ],
                  ),
                  trailing: IconButton(
                    icon: const Icon(Icons.edit, color: Colors.blue),
                    onPressed: () => _showStatusDialog(order),
                  ),
                ),
              );
            },
          ),
    );
  }

  Color _getStatusColor(String? status) {
    switch (status?.toLowerCase()) {
      case 'pending': return Colors.orange;
      case 'processing': return Colors.blue;
      case 'out for delivery': return Colors.purple;
      case 'delivered': return Colors.green;
      case 'cancelled': return Colors.red;
      default: return Colors.grey;
    }
  }
}
