import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import 'package:provider/provider.dart';
import '../services/api_service.dart';
import '../providers/auth_provider.dart';
import '../config.dart' as activeConfig;

class CartScreen extends StatefulWidget {
  final bool autoCheckout;

  CartScreen({Key? key, this.autoCheckout = false}) : super(key: key);

  @override
  _CartScreenState createState() => _CartScreenState();
}

class _CartScreenState extends State<CartScreen> {
  List<dynamic> _cartItems = [];
  double _total = 0;
  bool _isLoading = true;
  String _userId = '';
  bool _checkoutPrompted = false;

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadCart();
    });
  }

  Future<void> _loadCart() async {
    final userId = Provider.of<AuthProvider>(context, listen: false).user?.id;
    if (userId == null || userId.isEmpty) {
      setState(() => _isLoading = false);
      return;
    }
    _userId = userId;

    try {
      final res = await ApiService.post('cart.php', {'action': 'view', 'user_id': _userId});
      if (res['status'] == 'success') {
        setState(() {
          _cartItems = res['data'];
          _total = (res['total'] is int) ? (res['total'] as int).toDouble() : (res['total'] ?? 0.0);
          _isLoading = false;
        });
        if (widget.autoCheckout && !_checkoutPrompted && _cartItems.isNotEmpty) {
          _checkoutPrompted = true;
          WidgetsBinding.instance.addPostFrameCallback((_) {
            if (mounted) {
              _showCheckoutDialog();
            }
          });
        }
      } else {
        setState(() { _cartItems = []; _total = 0; _isLoading = false; });
      }
    } catch (e) {
      if (mounted) setState(() => _isLoading = false);
    }
  }

  void _removeItem(dynamic cartId) async {
    try {
      await ApiService.post('cart.php', {'action': 'remove', 'user_id': _userId, 'cart_id': cartId});
      _loadCart();
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Failed to remove item: $e')));
    }
  }

  void _showCheckoutDialog() {
    final streetCtrl = TextEditingController();
    final cityCtrl = TextEditingController();
    final zipCtrl = TextEditingController();
    final phoneCtrl = TextEditingController();
    
    showDialog(
      context: context,
      builder: (ctx) => AlertDialog(
        title: Text('Shipping Address', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
        content: SingleChildScrollView(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              TextField(controller: streetCtrl, decoration: InputDecoration(labelText: 'Street')),
              TextField(controller: cityCtrl, decoration: InputDecoration(labelText: 'City')),
              TextField(controller: zipCtrl, decoration: InputDecoration(labelText: 'ZIP Code')),
              TextField(controller: phoneCtrl, decoration: InputDecoration(labelText: 'Phone'), keyboardType: TextInputType.phone),
            ],
          ),
        ),
        actions: [
          TextButton(onPressed: () => Navigator.pop(ctx), child: Text('Cancel')),
          ElevatedButton(
            onPressed: () {
              if (streetCtrl.text.isEmpty || cityCtrl.text.isEmpty || zipCtrl.text.isEmpty || phoneCtrl.text.isEmpty) {
                ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Please fill all fields')));
                return;
              }
              Navigator.pop(ctx);
              _checkout(streetCtrl.text, cityCtrl.text, zipCtrl.text, phoneCtrl.text);
            },
            child: Text('Place Order'),
          )
        ],
      )
    );
  }

  void _checkout(String st, String cy, String zp, String ph) async {
    setState(() => _isLoading = true);
    try {
      final res = await ApiService.post('cart.php', {
        'action': 'checkout', 'user_id': _userId,
        'street': st, 'city': cy, 'zip': zp, 'phone': ph
      });
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(res['message'])));
      if (res['status'] == 'success') {
        _loadCart();
      }
    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Checkout failed: $e')));
    } finally {
      if (mounted) setState(() => _isLoading = false);
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
            Text('Flora Ai Cart', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
          ],
        ),
        backgroundColor: Colors.green[700],
      ),
      body: _isLoading 
        ? Center(child: CircularProgressIndicator())
        : _cartItems.isEmpty 
          ? Center(child: Text('Your cart is empty', style: GoogleFonts.poppins(fontSize: 18)))
          : Column(
              children: [
                Expanded(
                  child: ListView.builder(
                    itemCount: _cartItems.length,
                    itemBuilder: (context, index) {
                      final item = _cartItems[index];
                      return Card(
                        margin: EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                        child: ListTile(
                          leading: Image.network(
                            activeConfig.Config.baseUrl.replaceAll('/api', '') + "/" + (item['image_url'] ?? ''),
                            width: 50, height: 50, fit: BoxFit.cover,
                            errorBuilder: (ctx, err, stack) => const Icon(Icons.image_not_supported),
                          ),
                          title: Text(item['name'], style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                          subtitle: Text('${item['quantity']} x ₹${item['price']}', style: GoogleFonts.poppins()),
                          trailing: IconButton(
                            icon: Icon(Icons.delete, color: Colors.red),
                            onPressed: () {
                              showDialog(
                                context: context,
                                builder: (ctx) => AlertDialog(
                                  title: Text('Remove Item?', style: GoogleFonts.poppins(fontWeight: FontWeight.bold)),
                                  content: Text('Are you sure you want to remove this item from your cart?', style: GoogleFonts.poppins()),
                                  actions: [
                                    TextButton(onPressed: () => Navigator.pop(ctx), child: Text('Cancel', style: GoogleFonts.poppins())),
                                    ElevatedButton(
                                      style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
                                      onPressed: () {
                                        Navigator.pop(ctx);
                                        _removeItem(item['cart_id']);
                                      },
                                      child: Text('Remove', style: GoogleFonts.poppins(color: Colors.white)),
                                    )
                                  ]
                                )
                              );
                            },
                          ),
                        ),
                      );
                    },
                  ),
                ),
                Container(
                  padding: EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    boxShadow: [BoxShadow(color: Colors.black12, blurRadius: 10, offset: Offset(0, -5))]
                  ),
                  child: Column(
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                        children: [
                          Text('Total:', style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold)),
                          Text('₹${_total.toStringAsFixed(2)}', style: GoogleFonts.poppins(fontSize: 20, fontWeight: FontWeight.bold, color: Colors.green[800])),
                        ],
                      ),
                      SizedBox(height: 16),
                      ElevatedButton(
                        onPressed: _showCheckoutDialog,
                        style: ElevatedButton.styleFrom(
                          backgroundColor: Colors.green[700],
                          minimumSize: Size(double.infinity, 50),
                          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12))
                        ),
                        child: Text('Checkout', style: GoogleFonts.poppins(fontSize: 18, color: Colors.white)),
                      )
                    ],
                  ),
                )
              ],
            ),
    );
  }
}
