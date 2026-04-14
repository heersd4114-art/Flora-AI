import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
import '../services/api_service.dart';
import 'package:http/http.dart' as http;

class AiStatusScreen extends StatefulWidget {
  @override
  _AiStatusScreenState createState() => _AiStatusScreenState();
}

class _AiStatusScreenState extends State<AiStatusScreen> {
  bool _isLoading = true;
  String _statusMessage = 'Checking AI Server Status...';
  bool _isOnline = false;

  @override
  void initState() {
    super.initState();
    _checkStatus();
  }

  Future<void> _checkStatus() async {
    setState(() => _isLoading = true);
    try {
      // User mapping: ai_status.php
      final response = await http.get(Uri.parse('${ApiService.baseUrl}/../ai_status.php'));
      final body = response.body.toLowerCase();
      
      setState(() {
        if (body.contains('online')) {
          _isOnline = true;
          _statusMessage = 'Flora AI Engine is Online and ready for image analysis.';
        } else {
          _isOnline = false;
          _statusMessage = 'Flora AI Engine is Offline. Please start the AI Python server on port 5001.';
        }
        _isLoading = false;
      });
    } catch (e) {
      setState(() {
        _isOnline = false;
        _statusMessage = 'Could not connect to the server to check AI status.\nError: $e';
        _isLoading = false;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('AI Status', style: GoogleFonts.poppins(color: Colors.white, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.green[700],
        iconTheme: const IconThemeData(color: Colors.white),
      ),
      body: Center(
        child: Padding(
          padding: const EdgeInsets.all(32),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(
                _isLoading ? Icons.cloud_sync : (_isOnline ? Icons.cloud_done : Icons.cloud_off),
                size: 100,
                color: _isLoading ? Colors.blue : (_isOnline ? Colors.green : Colors.red),
              ),
              const SizedBox(height: 24),
              Text(
                _isLoading ? 'Polling...' : (_isOnline ? 'System Operational' : 'System Offline'),
                style: GoogleFonts.poppins(fontSize: 24, fontWeight: FontWeight.bold),
              ),
              const SizedBox(height: 16),
              Text(
                _statusMessage,
                textAlign: TextAlign.center,
                style: GoogleFonts.poppins(fontSize: 16, color: Colors.grey[700]),
              ),
              const SizedBox(height: 40),
              if (!_isLoading)
                ElevatedButton.icon(
                  onPressed: _checkStatus,
                  icon: const Icon(Icons.refresh),
                  label: Text('Refresh Status', style: GoogleFonts.poppins()),
                  style: ElevatedButton.styleFrom(
                    padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                    backgroundColor: Colors.green[700],
                    foregroundColor: Colors.white,
                  ),
                )
              else 
                const CircularProgressIndicator(color: Colors.green),
            ],
          ),
        ),
      ),
    );
  }
}
