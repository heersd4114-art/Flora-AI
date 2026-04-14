import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';

class OrderHistoryScreen extends StatefulWidget {
  @override
  _OrderHistoryScreenState createState() => _OrderHistoryScreenState();
}

class _OrderHistoryScreenState extends State<OrderHistoryScreen> {
  List<dynamic> _orders = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadOrders();
    });
  }

  Future<void> _loadOrders() async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
    if (userId == null || userId.isEmpty) {
      setState(() => _isLoading = false);
      return;
    }
    
    try {
      final res = await ApiService.post('orders.php', {'user_id': userId});
      if (res['status'] == 'success') {
        setState(() {
          _orders = res['data'];
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
        title: Text('My Orders', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        backgroundColor: Colors.green[700],
      ),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator())
        : _orders.isEmpty 
          ? Center(child: Text('No orders found', style: GoogleFonts.poppins(fontSize: 18)))
          : ListView.builder(
              padding: EdgeInsets.all(16),
              itemCount: _orders.length,
              itemBuilder: (context, index) {
                final order = _orders[index];
                return Card(
                  elevation: 2,
                  margin: EdgeInsets.only(bottom: 20),
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                  child: Padding(
                    padding: EdgeInsets.all(16),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Text('Order #${order['order_id']}', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 16)),
                                Text('${order['created_at']}', style: GoogleFonts.poppins(fontSize: 12, color: Colors.grey[600])),
                              ],
                            ),
                            Text('₹${order['total_amount']}', style: GoogleFonts.poppins(fontWeight: FontWeight.bold, fontSize: 18, color: Colors.green[800])),
                          ],
                        ),
                        SizedBox(height: 20),
                        
                        // Timeline Stepper (Amazon Style)
                        LayoutBuilder(
                          builder: (context, constraints) {
                            String status = (order['status'] ?? '').toString().toLowerCase();
                            int currentStep = 0;
                            if (status == 'processing') currentStep = 1;
                            else if (status == 'shipped') currentStep = 2;
                            else if (status == 'delivered') currentStep = 3;

                            List<String> steps = ['Placed', 'Processing', 'Shipped', 'Delivered'];
                            return Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: List.generate(4, (i) {
                                bool isActive = i <= currentStep;
                                return Expanded(
                                  child: Column(
                                    children: [
                                      Row(
                                        children: [
                                          Expanded(child: Container(height: 3, color: i == 0 ? Colors.transparent : (isActive ? Colors.green : Colors.grey[300]))),
                                          Container(
                                            width: 20, height: 20,
                                            decoration: BoxDecoration(shape: BoxShape.circle, color: isActive ? Colors.green : Colors.grey[300]),
                                            child: Icon(Icons.check, size: 12, color: isActive ? Colors.white : Colors.transparent),
                                          ),
                                          Expanded(child: Container(height: 3, color: i == 3 ? Colors.transparent : (i < currentStep ? Colors.green : Colors.grey[300]))),
                                        ],
                                      ),
                                      SizedBox(height: 4),
                                      Text(steps[i], style: GoogleFonts.poppins(fontSize: 10, fontWeight: isActive ? FontWeight.bold : FontWeight.normal, color: isActive ? Colors.black87 : Colors.grey)),
                                    ],
                                  ),
                                );
                              }),
                            );
                          }
                        ),
                        
                        SizedBox(height: 16),
                        Divider(),
                        SizedBox(height: 8),
                        
                        // Item List
                        if (order['items'] != null) ...[
                          Text('Items in Order:', style: GoogleFonts.poppins(fontWeight: FontWeight.w600, fontSize: 13)),
                          SizedBox(height: 8),
                          ...List.generate((order['items'] as List).length, (j) {
                            var item = order['items'][j];
                            return Padding(
                              padding: EdgeInsets.only(bottom: 8),
                              child: Row(
                                children: [
                                  ClipRRect(
                                    borderRadius: BorderRadius.circular(6),
                                    child: Image.network(
                                      ApiService.baseUrl.replaceAll('/api/', '/') + (item['image_url'] ?? ''),
                                      width: 40, height: 40, fit: BoxFit.cover,
                                      errorBuilder: (c,e,s) => Container(width: 40, height: 40, color: Colors.grey[300], child: Icon(Icons.shopping_bag, size: 20))
                                    ),
                                  ),
                                  SizedBox(width: 12),
                                  Expanded(
                                    child: Text('${item['name']}', style: GoogleFonts.poppins(fontSize: 13, fontWeight: FontWeight.w500)),
                                  ),
                                  Text('x${item['quantity']}', style: GoogleFonts.poppins(fontSize: 13, color: Colors.grey[700])),
                                ],
                              ),
                            );
                          })
                        ]
                      ],
                    )
                  )
                );
              },
            ),
    );
  }
}
