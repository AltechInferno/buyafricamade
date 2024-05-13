import 'package:flutter/material.dart';
import 'package:google_fonts/google_fonts.dart';
class BAMOptionTile extends StatelessWidget {
  const BAMOptionTile({Key key, @required this.title, @required this.icon}) : super(key: key);
  final String title;
  final Widget icon;
  @override
  Widget build(BuildContext context) {
    return Container(
      // margin: EdgeInsets.only(bottom: 20.0),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20.0),
        boxShadow: <BoxShadow>[
          BoxShadow(
            color: Color(0XFF000000).withOpacity(0.05),
            offset: Offset(0.0, 1.0),
            blurRadius: 8.0,
          ),
        ],
      ),
      child: ListTile(
          title: Text(
            title,
            style: GoogleFonts.poppins(
                fontSize: 14.0,
                fontWeight: FontWeight.w400,
                letterSpacing: 0,
                height: 1.5),
          ),
          shape:
          RoundedRectangleBorder(borderRadius: BorderRadius.circular(20.0)),
          trailing: icon),
    );
  }
}
