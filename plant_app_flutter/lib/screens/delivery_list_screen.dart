import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';

class DeliveryListScreen extends StatefulWidget {
  @override
  _DeliveryListScreenState createState() => _DeliveryListScreenState();
}

class _DeliveryListScreenState extends State<DeliveryListScreen> {
  bool _isLoading = true;
  List<dynamic> _deliveries = [];

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadDeliveries();
    });
  }

  Future<void> _loadDeliveries() async {
    final auth = Provider.of<AuthProvider>(context, listen: false);
    final partnerId = auth.user?.id;
    if (partnerId == null || partnerId.isEmpty) {
      if (mounted) setState(() => _isLoading = false);
      return;
    }
    
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.post('partner_orders.php', {'partner_id': partnerId});
      if (res['status'] == 'success' || res.containsKey('data')) {
        setState(() {
          _deliveries = res['data'] ?? res;
          _isLoading = false;
        });
      } else {
        setState(() => _isLoading = false);
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  Future<void> _updateStatus(dynamic shipmentId, String newStatus) async {
    try {
      final res = await ApiService.post('update_status.php', {
        'shipment_id': shipmentId,
        'status': newStatus
      });
      final resStr = res.toString();
      if (resStr.contains('Success') || resStr.contains('updated') || res['status'] == 'success') {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Shipment marked as $newStatus')));
        _loadDeliveries();
      } else {
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Update: ${res['message'] ?? resStr}')));
        _loadDeliveries();
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Error updating status')));
    }
  }

  void _showStatusDialog(dynamic delivery) {
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Update Shipment #${delivery['shipment_id']}'),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            ListTile(title: const Text('Picked Up'), onTap: () { Navigator.pop(ctx); _updateStatus(delivery['shipment_id'], 'Picked Up'); }),
            ListTile(title: const Text('In Transit'), onTap: () { Navigator.pop(ctx); _updateStatus(delivery['shipment_id'], 'In Transit'); }),
            ListTile(title: const Text('Out for Delivery'), onTap: () { Navigator.pop(ctx); _updateStatus(delivery['shipment_id'], 'Out for Delivery'); }),
            ListTile(title: const Text('Mark as Delivered', style: TextStyle(color: Colors.green, fontWeight: FontWeight.bold)), onTap: () { Navigator.pop(ctx); _updateStatus(delivery['shipment_id'], 'Delivered'); }),
          ],
        ),
      )
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Active Deliveries', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.orange[800],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: _isLoading 
        ? const Center(child: CircularProgressIndicator())
        : _deliveries.isEmpty 
            ? Center(child: Text('No active deliveries found', style: GoogleFonts.poppins(fontSize: 16)))
            : ListView.builder(
                padding: const EdgeInsets.all(16),
                itemCount: _deliveries.length,
                itemBuilder: (context, index) {
                  final delivery = _deliveries[index];
                  final items = delivery['items'] as List<dynamic>? ?? [];

                  return Card(
                    elevation: 4,
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                    margin: const EdgeInsets.only(bottom: 20),
                    child: Padding(
                      padding: const EdgeInsets.all(16),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                            children: [
                              Text('SHIPMENT #${delivery['shipment_id']}', style: GoogleFonts.poppins(fontWeight: FontWeight.w800, color: Colors.grey[700])),
                              Container(
                                padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                                decoration: BoxDecoration(
                                  color: Colors.orange[50],
                                  borderRadius: BorderRadius.circular(20),
                                  border: Border.all(color: Colors.orange[200]!)
                                ),
                                child: Text(delivery['status'] ?? 'PENDING', style: GoogleFonts.poppins(fontSize: 10, fontWeight: FontWeight.bold, color: Colors.orange[900])),
                              )
                            ],
                          ),
                          const Divider(height: 24),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Icon(Icons.person, size: 20, color: Colors.blueGrey),
                              const SizedBox(width: 8),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(delivery['customer_name'] ?? 'Guest', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16)),
                                    Text(delivery['customer_phone'] ?? '', style: GoogleFonts.poppins(color: Colors.grey[600], fontSize: 13)),
                                  ],
                                ),
                              ),
                            ],
                          ),
                          const SizedBox(height: 12),
                          Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              const Icon(Icons.location_on, size: 20, color: Colors.redAccent),
                              const SizedBox(width: 8),
                              Expanded(child: Text(delivery['address'] ?? 'No address provided', style: GoogleFonts.poppins(fontSize: 14))),
                            ],
                          ),
                          const SizedBox(height: 20),
                          Text('PACKAGE CONTENTS', style: GoogleFonts.poppins(fontSize: 11, fontWeight: FontWeight.bold, color: Colors.grey[500], letterSpacing: 1.2)),
                          const SizedBox(height: 8),
                          ...items.map((item) => Padding(
                            padding: const EdgeInsets.symmetric(vertical: 4),
                            child: Row(
                              children: [
                                Container(
                                  width: 32,
                                  height: 32,
                                  decoration: BoxDecoration(
                                    borderRadius: BorderRadius.circular(6),
                                    image: DecorationImage(image: NetworkImage(ApiService.baseUrl.replaceAll('/api/', '/') + (item['image_url'] ?? '')), fit: BoxFit.cover)
                                  ),
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(item['name'] ?? 'Product', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13)),
                                      Text('ID: #FLORA-${item['product_id']}', style: GoogleFonts.poppins(fontSize: 10, color: Colors.grey)),
                                    ],
                                  ),
                                ),
                                Text('x${item['quantity']}', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, color: Colors.orange[800])),
                              ],
                            ),
                          )).toList(),
                          const SizedBox(height: 20),
                          SizedBox(
                            width: double.infinity,
                            child: ElevatedButton(
                              onPressed: () => _showStatusDialog(delivery),
                              style: ElevatedButton.styleFrom(
                                backgroundColor: Colors.orange[800],
                                foregroundColor: Colors.white,
                                padding: const EdgeInsets.symmetric(vertical: 12),
                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
                              ),
                              child: Text('UPDATE STATUS', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                            ),
                          )
                        ],
                      ),
                    ),
                  );
                },
              ),
    );
  }
}
