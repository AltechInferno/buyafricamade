import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';


class BAMAppBarTitle extends StatelessWidget {
   BAMAppBarTitle({Key key, @required this.title, this.color}) : super(key: key);
  final String title;
  Color color;
  @override
  Widget build(BuildContext context) {
    return Text(title, style: GoogleFonts.poppins(
      fontSize: 24.0,
      fontWeight: FontWeight.w700,
      letterSpacing: -0.30,
      color: color,
    ),
    textAlign: TextAlign.center,
    );
  }
}
